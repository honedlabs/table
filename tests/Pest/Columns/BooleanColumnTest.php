<?php

declare(strict_types=1);

use Honed\Table\Columns\BooleanColumn;

beforeEach(function () {
    $this->param = 'is_active';
    $this->column = BooleanColumn::make($this->param);
});

it('makes', function () {
    expect($this->column)
        ->getName()->toBe($this->param)
        ->getLabel()->toBe('Is active')
        ->getType()->toBe('boolean')
        ->getTrueLabel()->toBe('True')
        ->getFalseLabel()->toBe('False');
});

it('has true label', function () {
    expect($this->column)
        ->getTrueLabel()->toBe('True')
        ->trueLabel('Yes')->toBe($this->column)
        ->getTrueLabel()->toBe('Yes');
});

it('has false label', function () {
    expect($this->column)
        ->getFalseLabel()->toBe('False')
        ->falseLabel('No')->toBe($this->column)
        ->getFalseLabel()->toBe('No');
});

it('sets labels', function () {
    expect($this->column)
        ->labels('Yes', 'No')->toBe($this->column)
        ->getTrueLabel()->toBe('Yes')
        ->getFalseLabel()->toBe('No');
});

it('applies', function () {

    expect($this->column)
        ->labels('Yes', 'No')->toBe($this->column)
        ->apply(true)->toBe('Yes')
        ->apply(false)->toBe('No');
});
