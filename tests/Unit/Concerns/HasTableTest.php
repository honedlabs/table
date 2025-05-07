<?php

declare(strict_types=1);

use Honed\Table\Concerns\HasTable;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\ProductTable;
use Illuminate\Database\Eloquent\Model;

class TableModel extends Model
{
    use HasTable;

    protected static $tableClass = ProductTable::class;
}

it('has a table', function () {
    expect(Product::table())
        ->toBeInstanceOf(ProductTable::class);
});

it('can set table', function () {
    $model = new TableModel();

    expect($model)
        ->table()->toBeInstanceOf(ProductTable::class);
});
