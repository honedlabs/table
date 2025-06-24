<?php

declare(strict_types=1);

use Honed\Table\Contracts\ViewScopeSerializeable;
use Honed\Table\Drivers\ArrayDriver;
use Honed\Table\Drivers\DatabaseDriver;
use Honed\Table\ViewManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\App;
use Workbench\App\Models\User;
use Workbench\App\Tables\UserTable;

beforeEach(function () {
    $this->manager = App::make(ViewManager::class);
});

it('gets store instance', function () {
    expect($this->manager)
        ->store()->toBeInstanceOf(DatabaseDriver::class)
        ->store('array')->toBeInstanceOf(ArrayDriver::class);
});

it('fails if unsupported driver is requested', function () {
    $this->manager->store('unsupported');
})->throws(InvalidArgumentException::class);

it('has default driver', function () {
    expect($this->manager)
        ->getDefaultDriver()->toBe('database')
        ->setDefaultDriver('array')->toBeNull()
        ->getDefaultDriver()->toBe('array');
});

it('serializes tables', function ($input, $expected) {
    expect($this->manager)
        ->serializeTable($input)->toBe($expected);
})->with([
    'class' => fn () => [UserTable::make(), UserTable::class],
    'string' => fn () => [UserTable::class, UserTable::class],
]);

it('serializes scopes', function ($input, $expected) {
    expect($this->manager)
        ->serializeScope($input)->toBe($expected);
})->with([
    'null' => fn () => [null, '__laravel_null'],
    'string' => fn () => ['test', 'test'],
    'numeric' => fn () => [1, '1'],
    'model' => fn () => [$user = User::factory()->create(), User::class.'|'.$user->getKey()],
    'serializable' => fn () => [new class() implements ViewScopeSerializeable
    {
        public function viewScopeSerialize(): string
        {
            return 'test';
        }
    }, 'test'],
]);

it('extends and forgets drivers', function () {
    $this->manager
        ->extend('custom', fn (string $name, Container $container) => new ArrayDriver($container->make(Dispatcher::class), $name)
        );

    expect($this->manager)
        ->store('custom')->toBeInstanceOf(ArrayDriver::class)
        ->forgetDrivers()->toBe($this->manager)
        ->store()->toBeInstanceOf(DatabaseDriver::class);
});

it('uses morph map', function () {
    expect($this->manager)
        ->useMorphMap(true)->toBe($this->manager)
        ->useMorphMap(false)->toBe($this->manager);
});

it('sets default scope resolver', function () {
    expect($this->manager)
        ->resolveScopeUsing(fn (string $driver) => $driver)->toBeNull();
});

it('delegates to driver', function () {
    expect($this->manager)
        ->get(UserTable::class, 'test', null)->toBeNull();
});
