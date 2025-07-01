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
    expect($this->interaction)
        ->getScope()->toBe($this->product);
});

it('has driver', function () {
    expect($this->interaction->getDriver())
        ->toBeInstanceOf(Decorator::class)
        ->getDriver()->toBeInstanceOf(DatabaseDriver::class);
});

it('gets view', function () {
    expect($this->interaction->get($this->table, 'Filter view'))
        ->toBeObject()
        ->name->toBe('Filter view')
        ->view->toBe(json_encode(['name' => 'test']));
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

it('creates view', function () {
    $this->interaction->create($this->table, 'Search view', ['name' => 'test']);

    expect($this->interaction->get($this->table, 'Search view'))
        ->toBeObject()
        ->name->toBe('Search view')
        ->view->toBe(json_encode(['name' => 'test']));
});

it('sets view', function () {
    $this->interaction->set($this->table, 'Filter view', ['name' => 'updated']);

    expect($this->interaction->get($this->table, 'Filter view'))
        ->toBeObject()
        ->name->toBe('Filter view')
        ->view->toBe(json_encode(['name' => 'updated']));
});

it('deletes view', function () {
    $this->interaction->delete($this->table, 'Filter view');

    expect($this->interaction->get($this->table, 'Filter view'))->toBeNull();
});
