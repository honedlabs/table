<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Columns\ImageColumn;

beforeEach(function () {
    $this->column = ImageColumn::make('image');
});

it('is type image', function () {
    expect($this->column)
        ->getType()->toBe(Column::IMAGE);
});

it('does not format null values', function () {
    expect($this->column)
        ->format(null)->toBeNull();
});

it('formats numbers', function () {
    $image = fake()->imageUrl();

    expect($this->column)
        ->format($image)->toBe($image);
});
