<?php

use Modules\Manjulika\Support\CyberScopeGuard;

test('blocks blatantly off-topic coding requests', function () {
    expect(CyberScopeGuard::isOffTopic('write me a python script to sort a list'))->toBeTrue();
    expect(CyberScopeGuard::isOffTopic('solve this equation: 2x + 3 = 9'))->toBeTrue();
    expect(CyberScopeGuard::isOffTopic('write a short poem about the rain'))->toBeTrue();
    expect(CyberScopeGuard::isOffTopic('what is the capital of France?'))->toBeTrue();
});

test('allows clear cyber incidents', function () {
    expect(CyberScopeGuard::isOffTopic('someone hacked my account'))->toBeFalse();
    expect(CyberScopeGuard::isOffTopic('mera account hack ho gaya'))->toBeFalse();
    expect(CyberScopeGuard::isOffTopic('I got a suspicious link and lost money'))->toBeFalse();
});

test('does not block cyber messages that merely mention code', function () {
    // Contains an off-topic-ish word ("code") but is a real incident — must pass through.
    expect(CyberScopeGuard::isOffTopic('they hacked my code repository and stole passwords'))->toBeFalse();
});

test('lets borderline non-keyword questions through to the LLM', function () {
    // No off-topic signal pattern → not blocked here; the system prompt decides.
    expect(CyberScopeGuard::isOffTopic('tell me a fact about the moon'))->toBeFalse();
});

test('refusal matches the user language', function () {
    expect(CyberScopeGuard::refusalFor('write me code'))->toContain('I can only help');
    expect(CyberScopeGuard::refusalFor('mujhe ek poem likh do'))->toContain('cyber safety mein help');
    expect(CyberScopeGuard::refusalFor('कृपया एक कविता लिखो'))->toContain('cyber safety mein help');
});
