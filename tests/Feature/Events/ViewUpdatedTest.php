<?php

declare(strict_types=1);

use Honed\Table\Events\ViewUpdated;
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
});

afterEach(function () {
    Event::assertDispatched(ViewUpdated::class, function (ViewUpdated $event) {
        return $event->table === $this->table
            && $event->name === $this->name
            && $event->scope === $this->scope
            && $event->view === $this->view;
    });
});

it('dispatches event', function () {
    ViewUpdated::dispatch(
        $this->table, $this->name, $this->scope, $this->view
    );
});

it('dispatch event when setting view', function () {

    Views::set(
        $this->table, $this->name, $this->scope, $this->view
    );
});
