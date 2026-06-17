<?php

namespace Modules\Manjulika\Support;

/**
 * Conservative, deterministic fast-path that short-circuits blatantly off-topic
 * requests before they reach the LLM. The system prompt remains the primary,
 * natural-language refusal mechanism; this guard only catches obvious cases and
 * deliberately errs on the side of letting messages through (it never blocks a
 * message that contains any cyber-safety term).
 */
class CyberScopeGuard
{
    /**
     * Cyber-safety terms (English + Hindi/Hinglish). If any of these appear, the
     * message is treated as potentially on-topic and is NOT blocked.
     */
    private const CYBER_TERMS = [
        'scam', 'fraud', 'fraudster', 'phish', 'hack', 'hacked', 'hacking', 'hacker',
        'otp', 'upi', 'paytm', 'gpay', 'phonepe', 'bank', 'banking', 'atm', 'debit',
        'credit card', 'password', 'passcode', 'pin', 'account', 'login', 'log in',
        'cyber', 'cybercrime', 'cybercrime.gov', '1930', 'virus', 'malware', 'ransomware',
        'spyware', 'trojan', 'phishing', 'spam', 'suspicious', 'fake', 'cheat', 'cheated',
        'blackmail', 'sextortion', 'extortion', 'identity', 'kyc', 'loan app', 'lottery',
        'refund', 'money', 'transaction', 'transfer', 'stolen', 'theft', 'sim', 'sim swap',
        'whatsapp', 'instagram', 'facebook', 'email', 'gmail', 'otp shared', 'link',
        // Hindi / Hinglish
        'paisa', 'paise', 'paise', 'thag', 'thug', 'thagi', 'thug liya', 'dhokha', 'fraud ho',
        'hack ho', 'account band', 'paise gaye', 'paise chale', 'number block', 'galat link',
        'pareshan', 'darr', 'dhamki',
    ];

    /**
     * Clear off-topic signals. These only cause a block when NO cyber term is present.
     */
    private const OFF_TOPIC_PATTERNS = [
        '/\b(write|give|generate|create|build|fix|debug)\b.{0,30}\b(code|program|programme|script|function|app|website|query|algorithm)\b/i',
        '/\b(python|java|javascript|typescript|c\+\+|c#|php|html|css|sql|react|laravel)\b/i',
        '/\b(solve|integrate|derivative|equation|calculate|factorial|multiply|divide)\b/i',
        '/\b(essay|poem|poetry|story|joke|riddle|recipe|lyrics|translate|translation|paragraph)\b/i',
        '/\b(capital of|who is the president|who is the prime minister|tallest|largest|distance between|weather in)\b/i',
        '/\b(homework|assignment|exam answer|quiz answer)\b/i',
    ];

    /**
     * Hinglish/Hindi signals used to pick the refusal language.
     */
    private const HINGLISH_TOKENS = [
        'hai', 'kya', 'kyu', 'kyun', 'mera', 'meri', 'mujhe', 'aap', 'tum', 'kaise',
        'kaisa', 'nahi', 'nai', 'kar', 'karo', 'karna', 'gaya', 'gaye', 'hua', 'ho gaya',
        'bata', 'batao', 'paise', 'paisa',
    ];

    public static function isOffTopic(string $message): bool
    {
        $text = trim($message);

        if ($text === '') {
            return false;
        }

        if (self::hasCyberTerms($text)) {
            return false;
        }

        return self::hasOffTopicSignal($text);
    }

    public static function refusalFor(string $message): string
    {
        if (self::isHinglish($message)) {
            return 'Sorry, main sirf cyber safety mein help kar sakta hoon — scam, fraud, hacked account jaisa kuch. Aisa kuch hua hai to bata do kya hua.';
        }

        return "Sorry, I can only help with cyber safety stuff — scams, fraud, hacked accounts, online threats. If something like that happened, tell me what's going on.";
    }

    private static function hasCyberTerms(string $text): bool
    {
        $lower = mb_strtolower($text);

        foreach (self::CYBER_TERMS as $term) {
            if (str_contains($lower, $term)) {
                return true;
            }
        }

        return false;
    }

    private static function hasOffTopicSignal(string $text): bool
    {
        foreach (self::OFF_TOPIC_PATTERNS as $pattern) {
            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
    }

    private static function isHinglish(string $message): bool
    {
        // Any Devanagari character → Hindi/Hinglish.
        if (preg_match('/\p{Devanagari}/u', $message) === 1) {
            return true;
        }

        $lower = mb_strtolower($message);

        foreach (self::HINGLISH_TOKENS as $token) {
            if (preg_match('/\b' . preg_quote($token, '/') . '\b/u', $lower) === 1) {
                return true;
            }
        }

        return false;
    }
}
