<?php

declare(strict_types=1);

use Honed\Table\Columns\Concerns\IsToggleable;

class IsToggleableTest
{
    use IsToggleable;
}

beforeEach(function () {
    $this->test = new IsToggleableTest;
});

it('is not `toggleable` by default', function () {
    expect($this->test->isToggleable())->toBeFalse();
});

it('sets toggleable', function () {
    $this->test->setToggleable(true);
    expect($this->test->isToggleable())->toBeTrue();
});

it('chains toggleable', function () {
    expect($this->test->toggleable())->toBeInstanceOf(IsToggleableTest::class)
        ->isToggleable()->toBeTrue();
});