<?php

declare(strict_types=1);

use Workbench\App\Models\Product;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->table = ProductTable::make();
});

it('has table with record', function () {
    expect($this->table->build()->toArray())
        ->toBeArray()
        ->not->toHaveKey('emptyState')
        ->toHaveKey('records')
        ->{'records'}
        ->scoped(fn ($records) => $records
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}
            ->scoped(fn ($record) => $record
                ->toBeArray()
                ->toHaveKeys([
                    'id',
                    'name',
                    'description',
                    // 'seller_name',
                    'public_id',
                    'class',
                    'operations',
                ])
                ->{'id'}->{'v'}->toBe($this->product->id)
                ->{'name'}->{'v'}->toBe($this->product->name)
                ->{'description'}->{'v'}->toBe($this->product->description)
                // ->{'seller_name'}->{'v'}->toBe('N/A')
                ->{'public_id'}->{'v'}->toBe($this->product->public_id)
                ->{'class'}->toBe('bg-black')
                ->{'operations'}->toBeArray()
            )
        );
});
