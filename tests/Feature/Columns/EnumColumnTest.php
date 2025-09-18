<?php

declare(strict_types=1);

use Honed\Table\Columns\EnumColumn;
use Workbench\App\Enums\Status;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->column = EnumColumn::make('status');
});

it('sets enum backing value', function () {
    expect($this->column)
        ->missingEnum()->toBeTrue()
        ->hasEnum()->toBeFalse()
        ->enum(Status::class)->toBe($this->column)
        ->missingEnum()->toBeFalse()
        ->hasEnum()->toBeTrue()
        ->getEnum()->toBe(Status::class);
});

it('transforms values', function () {
    expect($this->column->enum(Status::class))
        ->transform(null)->toBeNull()
        ->transform(Status::Available)->toBe(Status::Available->name)
        ->transform('available')->toBe(Status::Available->name);
});