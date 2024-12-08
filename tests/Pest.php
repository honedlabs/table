<?php

use Honed\Table\Tests\TestCase;
use Workbench\App\Models\Product;
use Honed\Modal\Tests\Stubs\ExampleTable;

uses(TestCase::class)->in(__DIR__);

function table(): ExampleTable
{
    return ExampleTable::make();
}

function product(): Product
{
    return Product::factory()->create();
}
