<?php

declare(strict_types=1);

use Honed\Table\Drivers\DatabaseDriver;
use Honed\Table\Events\ViewDeleted;
use Honed\Table\Events\ViewsPurged;
use Honed\Table\Events\ViewUpdated;
use Honed\Table\Facades\Views;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Event;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = ProductTable::make();

    $this->scope = User::factory()->create();

    $this->actingAs($this->scope);

    $this->decorator = Views::store();

    /** @var DatabaseDriver */
    $this->driver = $this->decorator->getDriver();

    $this->driver->set(
        $this->table, 'Filter view', $this->scope, ['name' => 'test']
    );

    Event::fake();
});

it('gets driver', function () {
    expect($this->decorator->getDriver())
        ->toBeInstanceOf(DatabaseDriver::class);
});

it('creates pending view interaction', function () {
    expect($this->decorator->for())
        ->getScope()
        ->scoped(fn ($scopes) => $scopes
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}->toBeInstanceOf(User::class)
        )
        ->load($this->table)
        ->scoped(fn ($views) => $views
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}
            ->scoped(fn ($view) => $view
                ->toBeObject()
                ->name->toBe('Filter view')
                ->view->toBe(json_encode(['name' => 'test']))
            )
        );
});

it('gets views', function () {
    $view = $this->decorator->get($this->table, 'Filter view', $this->scope);

    expect($view)
        ->toBeObject()
        ->name->toBe('Filter view');
});

it('lists views', function () {
    $views = $this->decorator->list($this->table, $this->scope);

    expect($views)
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($view) => $view
            ->name->toBe('Filter view')
            ->view->toBe(json_encode(['name' => 'test']))
        );
});

it('gets stored', function () {
    $views = $this->decorator->stored($this->table);

    expect($views)
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($view) => $view
            ->name->toBe('Filter view')
            ->view->toBe(json_encode(['name' => 'test']))
        );
});

it('errors if driver does not support stored', function () {
    Views::store('array')->stored($this->table);
})->throws(RuntimeException::class);

it('gets scoped', function () {
    $views = $this->decorator->scoped($this->scope);

    expect($views)
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}
        ->scoped(fn ($view) => $view
            ->name->toBe('Filter view')
            ->view->toBe(json_encode(['name' => 'test']))
        );
});

it('errors if driver does not support scoped', function () {
    Views::store('array')->scoped($this->scope);
})->throws(RuntimeException::class);

it('sets view', function () {
    $this->decorator->set(
        $this->table, 'New view', $this->scope, ['name' => 'updated']
    );

    expect($this->decorator->get($this->table, 'New view', $this->scope))
        ->toBeObject()
        ->name->toBe('New view')
        ->view->toBe(json_encode(['name' => 'updated']));

    Event::assertDispatched(ViewUpdated::class);
});

it('deletes view', function () {
    $this->decorator->delete($this->table, 'Filter view', $this->scope);

    expect($this->decorator->get($this->table, 'Filter view', $this->scope))
        ->toBeNull();

    Event::assertDispatched(ViewDeleted::class);
});

it('purges all views', function () {
    $this->decorator->purge();

    expect($this->decorator->stored($this->table))
        ->toBeArray()
        ->toHaveCount(0);

    Event::assertDispatched(ViewsPurged::class);
});

it('purges views for a table', function () {
    $this->decorator->purge($this->table);

    expect($this->decorator->stored($this->table))
        ->toBeArray()
        ->toHaveCount(0);

    Event::assertDispatched(ViewsPurged::class);
});

it('calls driver methods', function () {
    $this->decorator->insert(
        $this->table, 'Filter view', $this->scope, ['name' => 'test']
    );
})->throws(UniqueConstraintViolationException::class);
