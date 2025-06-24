<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Columns\DateColumn;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->column = DateColumn::make('created_at');
});

it('is type date time', function () {
    expect($this->column)
        ->getType()->toBe(Column::DATE)
        ->getPlaceholder()->toBe('-');
});

it('does not format null values', function () {
    expect($this->column)
        ->format(null)->toBeNull();
});

it('formats date times', function () {
    expect($this->column)
        ->format(Carbon::now())->toBe('2000-01-01');
});
