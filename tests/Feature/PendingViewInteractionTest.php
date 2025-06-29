<?php

declare(strict_types=1);

use Honed\Table\Drivers\DatabaseDriver;
use Honed\Table\Drivers\Decorator;
use Honed\Table\Facades\Views;
use Illuminate\Support\Facades\DB;
use Workbench\App\Models\Product;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->interaction = Views::for($this->product);

    $this->table = ProductTable::make();

    /** @var DatabaseDriver */
    $this->driver = $this->interaction->getDriver();

    DB::table($this->driver->getTableName())->insert([
        'name' => 'Filter view',
        'table' => Views::serializeTable($this->table),
        'scope' => Views::serializeScope($this->product),
        'view' => json_encode(['name' => 'test']),
    ]);
});

it('has scope', function () {
    $product = Product::factory()->create();

    $this->interaction->for($product);

    expect($this->interaction->getScope())
        ->toBeArray()
        ->toHaveCount(2)
        ->each(fn ($scope) => $scope->toBeInstanceOf(Product::class));
});

it('has driver', function () {
    expect($this->interaction->getDriver())
        ->toBeInstanceOf(Decorator::class)
        ->getDriver()->toBeInstanceOf(DatabaseDriver::class);
});

it('lists views', function () {
    expect($this->interaction->list($this->table))
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($view) => $view
            ->name->toBe('Filter view')
            ->view->toBe(json_encode(['name' => 'test']))
        );
});

it('gets stored views', function () {
    expect($this->interaction->stored($this->table))
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($view) => $view
            ->name->toBe('Filter view')
            ->view->toBe(json_encode(['name' => 'test']))
        );
});

it('gets scoped views', function () {
    expect($this->interaction->scoped())
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($view) => $view
            ->name->toBe('Filter view')
            ->view->toBe(json_encode(['name' => 'test']))
        );
});
