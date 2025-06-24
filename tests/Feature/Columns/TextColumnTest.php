<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Columns\TextColumn;

beforeEach(function () {
    $this->column = TextColumn::make('name');
});

it('is type text', function () {
    expect($this->column)
        ->getType()->toBe(Column::TEXT)
        ->getPlaceholder()->toBe('N/A');
});

it('does not format null values', function () {
    expect($this->column)
        ->format(null)->toBeNull();
});

it('formats text', function () {
    expect($this->column->limit(1))
        ->format('John Doe')->toBe('J...');
});
