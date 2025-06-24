<?php

declare(strict_types=1);

use Honed\Table\Facades\Views;
use Illuminate\Support\Facades\DB;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;
use Workbench\App\Tables\UserTable;

beforeEach(function () {
    /** @var Honed\Table\Drivers\DatabaseDriver */
    $this->driver = Views::store('database');

    $this->table = Views::serializeTable(ProductTable::make());

    $this->user = User::factory()->create();

    $this->scope = Views::serializeScope($this->user);

    $this->actingAs($this->user);

    DB::table($this->driver->getTableName())->insert([
        'name' => 'Filter view',
        'table' => $this->table,
        'scope' => $this->scope,
        'view' => json_encode(['name' => 'test']),
    ]);
});

it('gets first matching view', function () {
    $view = $this->driver->get($this->table, 'Filter view', $this->scope);

    expect($view)
        ->toBeObject()
        ->name->toBe('Filter view')
        ->table->toBe($this->table)
        ->scope->toBe($this->scope)
        ->view->toBe(json_encode(['name' => 'test']));
});

it('lists views', function () {
    $views = $this->driver->list($this->table, $this->scope);

    expect($views)
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($view) => $view
            ->name->toBe('Filter view')
            ->view->toBe(json_encode(['name' => 'test']))
        );
});

it('sets existing view', function () {
    $this->driver->set($this->table, 'Filter view', $this->scope, ['name' => 'updated']);

    $this->assertDatabaseCount($this->driver->getTableName(), 1);

    $this->assertDatabaseHas($this->driver->getTableName(), [
        'name' => 'Filter view',
        'table' => $this->table,
        'scope' => $this->scope,
        'view' => json_encode(['name' => 'updated']),
    ]);
});

it('sets new view', function () {
    $this->driver->set($this->table, 'Search view', $this->scope, ['name' => 'created']);

    $this->assertDatabaseCount($this->driver->getTableName(), 2);

    $this->assertDatabaseHas($this->driver->getTableName(), [
        'name' => 'Search view',
        'table' => $this->table,
        'scope' => $this->scope,
        'view' => json_encode(['name' => 'created']),
    ]);
});

it('inserts a view', function () {
    $this->driver->insert($this->table, 'Search view', $this->scope, ['name' => 'created']);

    $this->assertDatabaseCount($this->driver->getTableName(), 2);

    $this->assertDatabaseHas($this->driver->getTableName(), [
        'name' => 'Search view',
        'table' => $this->table,
        'scope' => $this->scope,
        'view' => json_encode(['name' => 'created']),
    ]);
});

it('updates a view', function () {
    $this->driver->update($this->table, 'Filter view', $this->scope, ['name' => 'updated']);

    $this->assertDatabaseCount($this->driver->getTableName(), 1);

    $this->assertDatabaseHas($this->driver->getTableName(), [
        'name' => 'Filter view',
        'table' => $this->table,
        'scope' => $this->scope,
        'view' => json_encode(['name' => 'updated']),
    ]);
});

it('deletes a view', function () {
    $this->driver->delete($this->table, 'Filter view', $this->scope);

    $this->assertDatabaseCount($this->driver->getTableName(), 0);
});

describe('purge', function () {
    beforeEach(function () {
        $this->userTable = Views::serializeTable(UserTable::make());
        $this->userScope = Views::serializeScope($this->user);

        DB::table($this->driver->getTableName())->insert([
            'name' => 'Filter view',
            'table' => $this->userTable,
            'scope' => $this->userScope,
            'view' => json_encode(['name' => 'test']),
        ]);
    });

    it('purges by table', function () {
        $this->driver->purge($this->table);

        $this->assertDatabaseCount($this->driver->getTableName(), 1);

        $this->assertDatabaseHas($this->driver->getTableName(), [
            'name' => 'Filter view',
            'table' => $this->userTable,
            'scope' => $this->userScope,
            'view' => json_encode(['name' => 'test']),
        ]);

        $this->assertDatabaseMissing($this->driver->getTableName(), [
            'name' => 'Filter view',
            'table' => $this->table,
            'scope' => $this->scope,
            'view' => json_encode(['name' => 'test']),
        ]);
    });

    it('purges all', function () {
        $this->driver->purge();

        $this->assertDatabaseEmpty($this->driver->getTableName());
    });
});
