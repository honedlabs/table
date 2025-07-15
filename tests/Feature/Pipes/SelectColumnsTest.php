<?php

declare(strict_types=1);

use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\NumericColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Pipes\SelectColumns;
use Honed\Table\Table;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->pipe = new SelectColumns();

    $this->table = Table::make()
        ->for(Product::class)
        ->selectable()
        ->select(['id'])
        ->columns([
            TextColumn::make('name')
                ->qualify()
                ->select('name as product_name'),

            NumericColumn::make('price'),

            BooleanColumn::make('best_seller')
                ->qualify(),

            TextColumn::make('name')
                ->qualify('users'),

            TextColumn::make('description')
                ->selectable(false)
                ->qualify(),
        ]);
});

it('selects', function () {
    $this->pipe->through($this->table);

    expect($this->table)
        ->isSelectable()->toBeTrue()
        ->getSelects()->toEqualCanonicalizing([
            'id',
            'products.name as product_name',
            'price',
            'products.best_seller',
            'users.name',
        ]);
});
