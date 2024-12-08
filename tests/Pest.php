<?php

use Honed\Table\Tests\TestCase;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\Category;
use Honed\Table\Tests\Stubs\ExampleTable;

uses(TestCase::class)->in(__DIR__);

function table(): ExampleTable
{
    return ExampleTable::make();
}

function product(): Product
{
    return Product::factory()->create();
}

function category(): Category
{
    return Category::factory()->create();
}
