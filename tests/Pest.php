<?php

use Honed\Table\Tests\Stubs\Category;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\Seller;
use Honed\Table\Tests\Stubs\Status;
use Honed\Table\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class)->in(__DIR__);

function product(?string $name = null): Product
{
    return seller()->products()->create([
        'public_id' => Str::uuid(),
        'name' => $name ?? fake()->unique()->words(2, true),
        'description' => fake()->sentence(),
        'price' => fake()->randomNumber(4),
        'best_seller' => fake()->boolean(),
        'status' => fake()->randomElement(Status::cases()),
        'created_at' => now()->subDays(fake()->randomNumber(2)),
    ]);
}

function category(?string $name = null): Category
{
    return Category::create([
        'name' => $name ?? fake()->unique()->word(),
    ]);
}

function seller(?string $name = null): Seller
{
    return Seller::create([
        'name' => $name ?? fake()->unique()->name(),
    ]);
}
