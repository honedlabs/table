<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Columns\TimeColumn;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->column = TimeColumn::make('created_at');
});

it('is type time', function () {
    expect($this->column)
        ->getType()->toBe(Column::TIME)
        ->getPlaceholder()->toBe('-');
});

it('does not format null values', function () {
    expect($this->column)
        ->format(null)->toBeNull();
});

it('formats time', function () {
    expect($this->column)
        ->format(Carbon::now())->toBe('00:00:00');
});
