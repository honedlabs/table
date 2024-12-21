<?php

use Honed\Table\Sorts\Sort;

beforeEach(function () {
    Sort::useAscending();
    $this->sort = Sort::make('created_at');
});

it('does not have a direction by default', function () {
    expect($this->sort->getDirection())->toBeNull();
    expect($this->sort->hasDirection())->toBeFalse();
    expect($this->sort->missingDirection())->toBeTrue();
});

it('can set a direction', function () {
    expect($this->sort->direction(Sort::Descending))->toBeInstanceOf(Sort::class)
        ->getDirection()->toBe(Sort::Descending);
});

it('can be set using setter', function () {
    $this->sort->setDirection(Sort::Descending);
    expect($this->sort->getDirection())->toBe(Sort::Descending);
});

it('does accept null values but defaults to the default direction', function () {
    $this->sort->setDirection(null);
    expect($this->sort->getDirection())->toBeNull();
});

it('checks if it has a direction', function () {
    expect($this->sort->hasDirection())->toBeFalse();
    expect($this->sort->missingDirection())->toBeTrue();
    $this->sort->setDirection(Sort::Descending);
    expect($this->sort->hasDirection())->toBeTrue();
    expect($this->sort->missingDirection())->toBeFalse();
});

it('has alias for direction checking', function () {
    expect($this->sort->isNotAgnostic())->toBeFalse();
    expect($this->sort->isAgnostic())->toBeTrue();
});

it('has shorthand for setting direction as descending', function () {
    expect($this->sort->desc())->toBeInstanceOf(Sort::class)
        ->getDirection()->toBe(Sort::Descending);
});

it('has shorthand for setting direction as ascending', function () {
    expect($this->sort->asc())->toBeInstanceOf(Sort::class)
        ->getDirection()->toBe(Sort::Ascending);
});

it('has shorthand for setting direction as agnostic', function () {
    expect($this->sort->agnostic())->toBeInstanceOf(Sort::class)
        ->getDirection()->toBeNull();
});

it('can be globally configured for default direction', function () {
    Sort::useDefaultDirection(Sort::Descending);
    expect($this->sort->getDefaultDirection())->toBe(Sort::Descending);
});

it('has shorthand for setting default direction as ascending', function () {
    Sort::useAscending();
    expect($this->sort->getDefaultDirection())->toBe(Sort::Ascending);
});

it('has shorthand for setting default direction as descending', function () {
    Sort::useDescending();
    expect($this->sort->getDefaultDirection())->toBe(Sort::Descending);
});

it('clears the default direction', function () {
    Sort::useDefaultDirection(null);
    expect($this->sort->getDefaultDirection())->toBe(Sort::Ascending);
});

it('prevents the default direction from being set to an invalid value', function () {
    Sort::useDefaultDirection('invalid');
})->throws(\InvalidArgumentException::class);

it('prevents the direction from being set to an invalid value', function () {
    $this->sort->setDirection('invalid');
})->throws(\InvalidArgumentException::class);

it('holds the active direction', function () {
    expect($this->sort->getActiveDirection())->toBeNull();
});

it('can set the active direction', function () {
    $this->sort->setActiveDirection(Sort::Descending);
    expect($this->sort->getActiveDirection())->toBe(Sort::Descending);
});

it('does not allow invalid active directions', function () {
    $this->sort->setActiveDirection('invalid');
})->throws(\InvalidArgumentException::class);
