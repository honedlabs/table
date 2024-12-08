<?php

use Honed\Table\Table;
use Honed\Table\Tests\Stubs\ExampleTable;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\Status;
use Honed\Table\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class)->in(__DIR__);

function exampleTable(): ExampleTable
{
    return ExampleTable::make();
}

function blankTable(): Table
{
    return Table::make();
}

function product(?string $name = null): Product
{
    return Product::factory()->create([
        'public_id' => Str::uuid(),
        'name' => $name ?? fake()->unique()->word(),
        'description' => fake()->sentence(),
        'price' => fake()->randomNumber(4),
        'best_seller' => fake()->boolean(),
        'status' => fake()->randomElement(Status::cases()),
        'created_at' => now()->subDays(fake()->randomNumber(2)),
    ]);
}
