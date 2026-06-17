<?php

namespace Modules\Manjulika\Http\Controllers;

use App\Ai\Agents\CyberSafetyAgent;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Manjulika\Models\CyberChatMessage;
use Modules\Manjulika\Models\CyberChatSession;
use Modules\Manjulika\Support\CyberScopeGuard;

class ChatController extends Controller
{
    private const SESSION_KEY = 'cyber_chat_db_session_id';
    private const MAX_HISTORY = 40;

    private function resolveDbSession(Request $request): CyberChatSession
    {
        $dbSessionId = session(self::SESSION_KEY);

        if ($dbSessionId) {
            $session = CyberChatSession::find($dbSessionId);
            if ($session) {
                $session->update(['last_active_at' => now()]);
                return $session;
            }
        }

        $session = CyberChatSession::create([
            'session_id'          => session()->getId(),
            'ip_address'          => $request->ip(),
            'user_agent'          => $request->userAgent(),
            'last_active_at'      => now(),
        ]);

        session([self::SESSION_KEY => $session->id]);

        return $session;
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $userMessage = trim($request->input('message'));
        $dbSession = $this->resolveDbSession($request);

        // On the very first turn the bot has asked for the user's name. Handle that
        // here, deterministically (no LLM call), and only once per session.
        $isFirstTurn = $dbSession->name === null && $dbSession->messages()->count() === 0;
        if ($isFirstTurn) {
            if ($this->isSkip($userMessage)) {
                return $this->respondWith($dbSession, $userMessage,
                    "No problem! Tell me — did you face some online scam, fraud, or a hacked account?");
            }
            if ($this->looksLikeName($userMessage)) {
                $name = Str::of($userMessage)->squish()->limit(80, '')->value();
                $dbSession->update(['name' => $name]);
                return $this->respondWith($dbSession, $userMessage,
                    "Nice to meet you, {$name}! 🙏 So tell me, what happened — some online scam, fraud, or a hacked account?");
            }
            // Otherwise it's clearly a problem, not a name → fall through to the
            // normal flow so we help right away; name stays null (it's optional).
        }

        // Fast-path: blatantly off-topic messages are declined without an LLM call.
        // The system prompt handles all subtler/borderline cases naturally.
        if (CyberScopeGuard::isOffTopic($userMessage)) {
            return $this->respondWith($dbSession, $userMessage, CyberScopeGuard::refusalFor($userMessage));
        }

        $dbSession->load('messages');
        $history = $dbSession->historyArray();

        $total = \count($history);
        $context = $total > self::MAX_HISTORY
            ? \array_slice($history, $total - self::MAX_HISTORY)
            : $history;

        try {
            $agent = new CyberSafetyAgent($context, $dbSession->name);
            $response = $agent->prompt($userMessage, provider: 'openai');
            $assistantText = $response->text;
        } catch (\Throwable $exception) {
            report($exception);
            return response()->json(['error' => 'Sorry, I could not process your request. Please try again.'], 500);
        }

        return $this->respondWith($dbSession, $userMessage, $assistantText);
    }

    private function respondWith(CyberChatSession $dbSession, string $userMessage, string $assistantText): JsonResponse
    {
        CyberChatMessage::insert([
            ['chat_session_id' => $dbSession->id, 'role' => 'user',      'content' => $userMessage,   'created_at' => now(), 'updated_at' => now()],
            ['chat_session_id' => $dbSession->id, 'role' => 'assistant', 'content' => $assistantText, 'created_at' => now(), 'updated_at' => now()],
        ]);

        return response()->json([
            'message'    => $assistantText,
            'session_id' => $dbSession->id,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $dbSessionId = session(self::SESSION_KEY);

        if (!$dbSessionId) {
            return response()->json(['history' => []]);
        }

        $session = CyberChatSession::with('messages')->find($dbSessionId);

        return response()->json([
            'history' => $session ? $session->historyArray() : [],
            'name'    => $session?->name,
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $dbSessionId = session(self::SESSION_KEY);

        if ($dbSessionId) {
            CyberChatMessage::where('chat_session_id', $dbSessionId)->delete();
        }

        return response()->json(['status' => 'cleared']);
    }

    /**
     * The user declined to share their name (e.g. "skip", "no", "nahi").
     */
    private function isSkip(string $message): bool
    {
        $normalized = mb_strtolower(trim($message));

        return \in_array($normalized, [
            'skip', 'no', 'nope', 'nah', 'na', 'nahi', 'nahin', 'later', 'no thanks', 'skip it',
        ], true);
    }

    /**
     * Heuristic: does this first message look like a name rather than a problem
     * description? Keeps a distress message ("someone hacked my bank") from being
     * stored as the user's name. Names are short, word-like, and carry no digits,
     * links, questions, or cyber-incident keywords.
     */
    private function looksLikeName(string $message): bool
    {
        $message = trim($message);

        if ($message === '' || mb_strlen($message) > 40) {
            return false;
        }
        if (str_word_count($message) > 4) {
            return false;
        }
        if (preg_match('/\d/', $message)) {
            return false;
        }
        if (str_contains($message, '?') || preg_match('#https?://#i', $message)) {
            return false;
        }

        $lower = mb_strtolower($message);
        $keywords = [
            'scam', 'fraud', 'hack', 'otp', 'upi', 'money', 'paise', 'rupee', 'account', 'bank',
            'card', 'link', 'call', 'message', 'blackmail', 'sextortion', 'phish', 'virus', 'malware',
            'steal', 'stolen', 'chori', 'thug', 'lost', 'help', 'problem',
        ];
        foreach ($keywords as $keyword) {
            if (str_contains($lower, $keyword)) {
                return false;
            }
        }

        return true;
    }
}
