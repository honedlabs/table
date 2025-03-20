<?php

declare(strict_types=1);

use Honed\Table\Columns\TextColumn;

beforeEach(function () {
    $this->param = 'name';
    $this->column = TextColumn::make($this->param);
});

it('makes', function () {
    expect($this->column)
        ->getName()->toBe($this->param)
        ->getLabel()->toBe(ucfirst($this->param))
        ->getType()->toBe('text');
});

it('has prefix', function () {
    expect($this->column)
        ->getPrefix()->toBeNull()
        ->prefix('Mr.')->toBe($this->column)
        ->getPrefix()->toBe('Mr.');
});

it('has suffix', function () {
    expect($this->column)
        ->getSuffix()->toBeNull()
        ->suffix(' Jr.')->toBe($this->column)
        ->getSuffix()->toBe(' Jr.');
});

it('has length', function () {
    expect($this->column)
        ->getLength()->toBeNull()
        ->length(10)->toBe($this->column)
        ->getLength()->toBe(10);
});

it('applies', function () {
    expect($this->column)
        ->fallback('-')->toBe($this->column)
        ->apply(null)->toBe('-')
        ->length(5)->toBe($this->column)
        ->apply('Joshua')->toBe('Joshu')
        ->prefix('Mr.')->toBe($this->column)
        ->apply('Joshua')->toBe('Mr.Jo')
        ->suffix('.')->toBe($this->column)
        ->apply('Joshua')->toBe('Mr.Jo');
});