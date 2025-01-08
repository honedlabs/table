<?php

declare(strict_types=1);

use Honed\Table\Actions\Concerns\IsDeselecting;

class IsDeselectingTest
{
    use IsDeselecting;
}

beforeEach(function () {
    $this->test = new IsDeselectingTest;
});

it('is not `deselecting` by default', function () {
    expect($this->test->isDeselecting())->toBeFalse();
});

it('sets deselect', function () {
    $this->test->setDeselecting(true);
    expect($this->test->isDeselecting())->toBeTrue();
});

it('chains deselect', function () {
    expect($this->test->deselect())
        ->toBeInstanceOf(IsDeselectingTest::class)
        ->isDeselecting()->toBeTrue();
});
