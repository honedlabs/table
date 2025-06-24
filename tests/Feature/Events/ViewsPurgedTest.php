<?php

declare(strict_types=1);

use Honed\Table\Events\ViewsPurged;
use Honed\Table\Facades\Views;
use Illuminate\Support\Facades\Event;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;
use Workbench\App\Tables\UserTable;

beforeEach(function () {
    Event::fake();

    $this->productTable = Views::serializeTable(ProductTable::make());

    $this->userTable = Views::serializeTable(UserTable::make());

    $this->name = 'Filter view';

    $this->scope = Views::serializeScope(User::factory()->create());

    $this->view = ['name' => 'test'];

    /** @var Honed\Table\Drivers\DatabaseDriver */
    $this->driver = Views::getDriver();

    Views::set(
        $this->productTable, $this->name, $this->scope, $this->view
    );

    Views::set(
        $this->userTable, $this->name, $this->scope, $this->view
    );
});

it('dispatches event', function () {
    ViewsPurged::dispatch();

    Event::assertDispatched(ViewsPurged::class, function (ViewsPurged $event) {
        return $event->tables === null;
    });
});

it('dispatch event when purging all views', function () {
    Views::purge();

    Event::assertDispatched(ViewsPurged::class, function (ViewsPurged $event) {
        return $event->tables === null;
    });

    $this->assertDatabaseEmpty($this->driver->getTableName());
});

it('dispatch event when purging specific views', function () {
    Views::purge($this->productTable);

    Event::assertDispatched(ViewsPurged::class, function (ViewsPurged $event) {
        return $event->tables === [$this->productTable];
    });

    $this->assertDatabaseCount($this->driver->getTableName(), 1);
});
