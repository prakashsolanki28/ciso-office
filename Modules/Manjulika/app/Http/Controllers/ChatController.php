<?php

namespace Modules\Manjulika\Http\Controllers;

use App\Ai\Agents\CyberSafetyAgent;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $agent = new CyberSafetyAgent($context);
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
}
