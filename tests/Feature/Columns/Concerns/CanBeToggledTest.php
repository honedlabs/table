<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('is toggleable by default', function () {
    expect($this->column)
        ->isToggleable()->toBeTrue()
        ->isNotToggleable()->toBeFalse()
        ->notToggleable()->toBe($this->column)
        ->isNotToggleable()->toBeTrue()
        ->toggleable()->toBe($this->column)
        ->isToggleable()->toBeTrue();
});

it('can set toggleable with default state', function () {
    expect($this->column)
        ->isNotToggledByDefault()->toBeTrue()
        ->isToggledByDefault()->toBeFalse()
        ->toggleable(true, true)->toBe($this->column)
        ->isToggleable()->toBeTrue()
        ->isToggledByDefault()->toBeTrue();
});

it('can set default toggled state', function () {
    expect($this->column)
        ->isNotToggledByDefault()->toBeTrue()
        ->isToggledByDefault()->toBeFalse()
        ->toggledByDefault()->toBe($this->column)
        ->isToggledByDefault()->toBeTrue()
        ->notToggledByDefault()->toBe($this->column)
        ->isNotToggledByDefault()->toBeTrue();
});

it('can set always state', function () {
    expect($this->column)
        ->isNotAlways()->toBeTrue()
        ->isAlways()->toBeFalse()
        ->always()->toBe($this->column)
        ->isAlways()->toBeTrue()
        ->notAlways()->toBe($this->column)
        ->isNotAlways()->toBeTrue();
});
