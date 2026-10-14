<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Attendance\Services\FaceMatcher;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');
});

it('matches an enrolled face descriptor and rejects a different one', function () {
    $matcher = new FaceMatcher;
    $enrolled = array_fill(0, 128, 0.1);
    $near = array_map(fn (float $value): float => $value + 0.01, $enrolled);
    $other = array_fill(0, 128, 0.9);

    expect($matcher->normalize([1, 2]))->toBeNull()
        ->and($matcher->normalize(array_merge($enrolled, ['nope'])))->toBeNull()
        ->and($matcher->matches($enrolled, $enrolled))->toBeTrue()
        ->and($matcher->matches($near, $enrolled))->toBeTrue()
        ->and($matcher->matches($other, $enrolled))->toBeFalse();
});
