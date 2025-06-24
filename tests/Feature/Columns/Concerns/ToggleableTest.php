<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('is toggleable by default', function () {
    expect($this->column)
        ->isToggleable()->toBeTrue()
        ->toggleable(false)->toBe($this->column)
        ->isToggleable()->toBeFalse();
});

it('can set toggleable with default state', function () {
    expect($this->column)
        ->isDefaultToggled()->toBeFalse()
        ->toggleable(true, true)->toBe($this->column)
        ->isToggleable()->toBeTrue()
        ->isDefaultToggled()->toBeTrue();
});

it('can set default toggled state', function () {
    expect($this->column)
        ->isDefaultToggled()->toBeFalse()
        ->defaultToggled()->toBe($this->column)
        ->isDefaultToggled()->toBeTrue();
});

it('can set always state', function () {
    expect($this->column)
        ->isAlways()->toBeFalse()
        ->always()->toBe($this->column)
        ->isAlways()->toBeTrue();
});
