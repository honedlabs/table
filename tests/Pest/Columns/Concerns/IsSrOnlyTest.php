<?php

declare(strict_types=1);

use Honed\Table\Columns\Concerns\IsSrOnly;

class IsSrOnlyTest
{
    use IsSrOnly;
}

beforeEach(function () {
    $this->test = new IsSrOnlyTest;
});

it('is not `srOnly` by default', function () {
    expect($this->test->isSrOnly())->toBeFalse();
});

it('sets srOnly', function () {
    $this->test->setSrOnly(true);
    expect($this->test->isSrOnly())->toBeTrue();
});

it('chains srOnly', function () {
    expect($this->test->srOnly())->toBeInstanceOf(IsSrOnlyTest::class)
        ->isSrOnly()->toBeTrue();
});