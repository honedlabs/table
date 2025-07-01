<?php

declare(strict_types=1);

namespace Honed\Table\Drivers;

use Honed\Table\Contracts\CanListViews;
use Honed\Table\Contracts\Driver;
use Honed\Table\Events\ViewCreated;
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
     * @param  (callable(): mixed)  $defaultScopeResolver
     */
    public function __construct(string $name, Driver $driver, callable $defaultScopeResolver)
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
     */
    public function for(mixed $scope = null): PendingViewInteraction
    {
        return (new PendingViewInteraction($this))
            ->for($scope ?? $this->defaultScope());
    }

    /**
     * Retrieve the view for the given table, name, and scope from storage.
     *
     * @return object|null
     */
    public function get(mixed $table, string $name, mixed $scope)
    {
        $table = Views::serializeTable($table);

        $scope = Views::serializeScope($scope);

        return $this->driver->get($table, $name, $scope);
    }

    /**
     * Retrieve the views for the given table and scopes from storage.
     *
     * @param  mixed|array<int, mixed>  $scopes
     * @return array<int, object>
     */
    public function list(mixed $table, mixed $scopes): array
    {
        $scopes = $this->resolveScopes($scopes);

        $table = Views::serializeTable($table);

        return $this->driver->list($table, $scopes);
    }

    /**
     * Get all the views stored for all tables.
     *
     * @return array<int, object>
     *
     * @throws RuntimeException
     */
    public function all(): array
    {
        $driver = $this->checkIfCanListViews();

        return $driver->all();
    }

    /**
     * Get the views stored for a given table or tables.
     *
     * @param  mixed|array<int, mixed>  $table
     * @return array<int, object>
     *
     * @throws RuntimeException
     */
    public function stored(mixed $table): array
    {
        $driver = $this->checkIfCanListViews();

        $table = $this->resolveTables($table);

        return $driver->stored($table);
    }

    /**
     * Get the views stored for a given scope or scopes.
     *
     * @param  mixed|array<int, mixed>  $scope
     * @return array<int, object>
     *
     * @throws RuntimeException
     */
    public function scoped(mixed $scope): array
    {
        $driver = $this->checkIfCanListViews();

        $scope = $this->resolveScopes($scope);

        return $driver->scoped($scope);
    }

    /**
     * Create a new view for the given table, name and scope.
     *
     * @param  array<string, mixed>  $view
     */
    public function create(mixed $table, string $name, mixed $scope, array $view): void
    {
        $table = Views::serializeTable($table);

        $scope = Views::serializeScope($scope);

        $this->driver->create($table, $name, $scope, $view);

        Event::dispatch(new ViewCreated($table, $name, $scope, $view));
    }

    /**
     * Set the view for the given table and scope.
     *
     * @param  array<string, mixed>  $view
     */
    public function set(mixed $table, string $name, mixed $scope, array $view): void
    {
        $table = Views::serializeTable($table);

        $scope = Views::serializeScope($scope);

        $this->driver->set($table, $name, $scope, $view);

        Event::dispatch(new ViewUpdated($table, $name, $scope, $view));
    }

    /**
     * Delete the view for the given table and scope from storage.
     */
    public function delete(mixed $table, string $name, mixed $scope): void
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
     */
    public function purge(mixed $table = null): void
    {
        if ($table !== null) {
            $table = $this->resolveTables($table);
        }

        $this->driver->purge($table);

        Event::dispatch(new ViewsPurged($table));
    }

    /**
     * Get the underlying view driver.
     */
    public function getDriver(): Driver
    {
        return $this->driver;
    }

    /**
     * Retrieve the default scope.
     */
    protected function defaultScope(): mixed
    {
        return ($this->defaultScopeResolver)();
    }

    /**
     * Resolve the scopes.
     *
     * @param  mixed|array<int, mixed>  $scopes
     * @return array<int, string>
     */
    protected function resolveScopes(mixed $scopes): array
    {
        $scopes = is_array($scopes) ? $scopes : func_get_args();

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
    protected function resolveTables(mixed $tables): array
    {
        $tables = is_array($tables) ? $tables : func_get_args();

        return array_map(
            static fn ($table) => Views::serializeTable($table),
            $tables
        );
    }

    /**
     * Check if the driver supports listing views.
     *
     * @throws RuntimeException
     */
    protected function checkIfCanListViews(): CanListViews
    {
        if (! $this->driver instanceof CanListViews) {
            throw new RuntimeException(
                "The [{$this->name}] driver does not support listing views."
            );
        }

        /** @var CanListViews */
        return $this->driver;
    }
}
