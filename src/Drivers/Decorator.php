<?php

declare(strict_types=1);

namespace Honed\Table\Drivers;

use Honed\Table\Contracts\CanListViews;
use Honed\Table\Contracts\Driver;
use Honed\Table\Events\ViewDeleted;
use Honed\Table\Events\ViewsPurged;
use Honed\Table\Events\ViewUpdated;
use Honed\Table\Facades\Views;
use Honed\Table\PendingViewInteraction;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;

class Decorator implements CanListViews, Driver
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The store's name.
     *
     * @var string
     */
    protected $name;

    /**
     * The driver instance.
     *
     * @var Driver
     */
    protected $driver;

    /**
     * The default scope resolver.
     *
     * @var callable(): mixed
     */
    protected $defaultScopeResolver;

    /**
     * Create a new driver decorator instance.
     *
     * @param  string  $name
     * @param  Driver  $driver
     * @param  (callable(): mixed)  $defaultScopeResolver
     */
    public function __construct($name, $driver, $defaultScopeResolver)
    {
        $this->name = $name;
        $this->driver = $driver;
        $this->defaultScopeResolver = $defaultScopeResolver;
    }

    /**
     * Dynamically call the underlying driver instance.
     *
     * @param  string  $name
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    public function __call($name, $parameters)
    {
        if (static::hasMacro($name)) {
            return $this->macroCall($name, $parameters);
        }

        return $this->getDriver()->{$name}(...$parameters);
    }

    /**
     * Create a pending view retrieval.
     *
     * @param  mixed|array<int, mixed>  $scope
     * @return PendingViewInteraction
     */
    public function for($scope = null)
    {
        return (new PendingViewInteraction($this))
            ->for($scope ?? $this->defaultScope());
    }

    /**
     * Retrieve the view for the given table, name, and scope from storage.
     *
     * @param  string|\Honed\Table\Table  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @return object|null
     */
    public function get($table, $name, $scope)
    {
        $table = Views::serializeTable($table);

        $scope = Views::serializeScope($scope);

        return $this->driver->get($table, $name, $scope);
    }

    /**
     * Retrieve the views for the given table and scopes from storage.
     *
     * @param  mixed  $table
     * @param  array<int, mixed>  $scopes
     * @return array<int, object>
     */
    public function list($table, $scopes)
    {
        $scopes = $this->resolveScopes($scopes);

        $table = Views::serializeTable($table);

        return $this->driver->list($table, $scopes);
    }

    /**
     * Get the views stored for a given table or tables.
     *
     * @param  mixed|array<int, mixed>  $table
     * @return array<int, object>
     */
    public function stored($table)
    {
        if (! $this->driver instanceof CanListViews) {
            throw new RuntimeException(
                "The [{$this->name}] driver does not support listing stored views."
            );
        }

        $table = $this->resolveTables($table);

        return $this->driver->stored($table);
    }

    /**
     * Get the views stored for a given scope or scopes.
     *
     * @param  mixed|array<int, mixed>  $scope
     * @return array<int, object>
     */
    public function scoped($scope)
    {
        if (! $this->driver instanceof CanListViews) {
            throw new RuntimeException(
                "The [{$this->name}] driver does not support listing scoped views."
            );
        }

        $scope = $this->resolveScopes($scope);

        return $this->driver->scoped($scope);
    }

    /**
     * Set the view for the given table and scope.
     *
     * @param  mixed  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @param  array<string, mixed>  $view
     * @return void
     */
    public function set($table, $name, $scope, $view)
    {
        $table = Views::serializeTable($table);

        $scope = Views::serializeScope($scope);

        $this->driver->set($table, $name, $scope, $view);

        Event::dispatch(new ViewUpdated($table, $name, $scope, $view));
    }

    /**
     * Delete the view for the given table and scope from storage.
     *
     * @param  mixed  $table
     * @param  string  $name
     * @param  mixed  $scope
     * @return void
     */
    public function delete($table, $name, $scope)
    {
        $table = Views::serializeTable($table);

        $scope = Views::serializeScope($scope);

        $this->driver->delete($table, $name, $scope);

        Event::dispatch(new ViewDeleted($table, $name, $scope));
    }

    /**
     * Purge all views for the given table.
     *
     * @param  mixed|array<int, mixed>|null  $table
     * @return void
     */
    public function purge($table = null)
    {
        if ($table !== null) {
            $table = $this->resolveTables($table);
        }

        $this->driver->purge($table);

        Event::dispatch(new ViewsPurged($table));
    }

    /**
     * Get the underlying view driver.
     *
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Retrieve the default scope.
     *
     * @return mixed
     */
    protected function defaultScope()
    {
        return ($this->defaultScopeResolver)();
    }

    /**
     * Resolve the scopes.
     *
     * @param  mixed|array<int, mixed>  $scopes
     * @return array<int, string>
     */
    protected function resolveScopes($scopes)
    {
        $scopes = is_array($scopes) ? $scopes : [$scopes];

        return array_map(
            static fn ($scope) => Views::serializeScope($scope),
            $scopes
        );
    }

    /**
     * Resolve the tables.
     *
     * @param  mixed|array<int, mixed>  $tables
     * @return array<int, string>
     */
    protected function resolveTables($tables)
    {
        $tables = is_array($tables) ? $tables : [$tables];

        return array_map(
            static fn ($table) => Views::serializeTable($table),
            $tables
        );
    }
}
