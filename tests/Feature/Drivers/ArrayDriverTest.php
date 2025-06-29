<?php

declare(strict_types=1);

use Honed\Table\Facades\Views;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;
use Workbench\App\Tables\UserTable;

beforeEach(function () {
    /** @var Honed\Table\Drivers\ArrayDriver */
    $this->driver = Views::store('array')->getDriver();

    $this->table = Views::serializeTable(ProductTable::make());

    $this->user = User::factory()->create();

    $this->scope = Views::serializeScope($this->user);

    $this->driver->set(
        $this->table, 'Filter view', $this->scope, ['name' => 'test']
    );
});

it('gets first matching view', function () {
    $view = $this->driver->get($this->table, 'Filter view', $this->scope);

    expect($view)
        ->toBeObject()
        ->name->toBe('Filter view')
        ->table->toBe($this->table)
        ->scope->toBe($this->scope)
        ->view->toEqual(['name' => 'test']);
});

it('lists views', function () {
    $views = $this->driver->list($this->table, $this->scope);

    expect($views)
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($view) => $view
            ->toBeObject()
            ->name->toBe('Filter view')
            ->view->toEqual(['name' => 'test'])
        );
});

it('creates a new view', function () {
    $this->driver->create($this->table, 'Filter view', $this->scope, ['name' => 'created']);

    expect($this->driver->get($this->table, 'Filter view', $this->scope))
        ->toBeObject()
        ->name->toBe('Filter view')
        ->view->toEqual(['name' => 'created']);
});

it('sets existing view', function () {
    $this->driver->set($this->table, 'Filter view', $this->scope, ['name' => 'updated']);

    expect($this->driver->get($this->table, 'Filter view', $this->scope))
        ->toBeObject()
        ->name->toBe('Filter view')
        ->view->toEqual(['name' => 'updated']);
});

it('sets new view', function () {
    $this->driver->set($this->table, 'Search view', $this->scope, ['name' => 'created']);

    expect($this->driver->get($this->table, 'Search view', $this->scope))
        ->toBeObject()
        ->name->toBe('Search view')
        ->view->toEqual(['name' => 'created']);
});

it('deletes a view', function () {
    $this->driver->delete($this->table, 'Filter view', $this->scope);

    expect($this->driver->get($this->table, 'Filter view', $this->scope))
        ->toBeNull();
});

describe('purge', function () {
    beforeEach(function () {
        $this->userTable = Views::serializeTable(UserTable::make());
        $this->userScope = Views::serializeScope($this->user);

        $this->driver->set(
            $this->userTable,
            'Filter view',
            $this->userScope,
            ['name' => 'test'],
        );
    });

    it('purges by table', function () {
        $this->driver->purge($this->table);

        expect($this->driver->list($this->table, $this->scope))
            ->toBeEmpty();

        expect($this->driver->list($this->userTable, $this->userScope))
            ->toHaveCount(1)
            ->{0}
            ->scoped(fn ($view) => $view
                ->toBeObject()
                ->name->toBe('Filter view')
                ->view->toEqual(['name' => 'test'])
            );
    });

    it('purges all', function () {
        $this->driver->purge();

        expect($this->driver->list($this->table, $this->scope))
            ->toHaveCount(0);
    });
});
