<?php

declare(strict_types=1);

use Honed\Table\Events\ViewDeleted;
use Honed\Table\Facades\Views;
use Illuminate\Support\Facades\Event;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    Event::fake();

    $this->table = Views::serializeTable(ProductTable::make());

    $this->name = 'Filter view';

    $this->scope = Views::serializeScope(User::factory()->create());

    $this->view = ['name' => 'test'];

    Views::set(
        $this->table, $this->name, $this->scope, $this->view
    );
});

afterEach(function () {
    Event::assertDispatched(ViewDeleted::class, function (ViewDeleted $event) {
        return $event->table === $this->table
            && $event->name === $this->name
            && $event->scope === $this->scope;
    });
});

it('dispatches event', function () {
    ViewDeleted::dispatch(
        $this->table, $this->name, $this->scope
    );
});

it('dispatch event when deletings view', function () {
    Views::delete(
        $this->table, $this->name, $this->scope
    );

    /** @var Honed\Table\Drivers\DatabaseDriver */
    $driver = Views::getDriver();

    $this->assertDatabaseMissing($driver->getTableName(), [
        'table' => $this->table,
        'name' => $this->name,
        'scope' => $this->scope,
    ]);
});
