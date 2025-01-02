<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('can set a string tooltip', function () {
    $this->column->setTooltip($p = 'Tooltip');
    expect($this->column->getTooltip())->toBe($p);
});

it('can set a closure tooltip', function () {
    $this->column->setTooltip(fn () => 'Tooltip');
    expect($this->column->getTooltip())->toBe('Tooltip');
});

it('prevents null values', function () {
    $this->column->setTooltip(null);
});

it('can chain tooltip', function () {
    expect($this->column->tooltip($p = 'Tooltip'))->toBeInstanceOf(Column::class);
    expect($this->column->getTooltip())->toBe($p);
});

it('checks for tooltip', function () {
    expect($this->column->hasTooltip())->toBeFalse();
    $this->column->setTooltip('Tooltip');
    expect($this->column->hasTooltip())->toBeTrue();
});

it('resolves a tooltip', function () {
    expect($this->column->tooltip(fn ($record) => $record.'.'))
        ->toBeInstanceOf(Column::class)
        ->resolveTooltip(['record' => 'Tooltip'])->toBe('Tooltip.');
});
