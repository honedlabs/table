<?php

declare(strict_types=1);

namespace Honed\Table\Facades;

use Honed\Table\ViewManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Honed\Table\Drivers\Decorator store(string|null $store = null) Get a view store instance
 * @method static \Honed\Table\Drivers\Decorator driver(string|null $name = null) Get a view store instance by name
 * @method static \Honed\Table\Drivers\ArrayDriver createArrayDriver(string $name) Create an instance of the array driver
 * @method static \Honed\Table\Drivers\DatabaseDriver createDatabaseDriver(string $name) Create an instance of the database driver
 * @method static string getDefaultDriver() Get the default driver name
 * @method static void setDefaultDriver(string $name) Set the default driver name
 * @method static \Honed\Table\ViewManager forgetDriver(string|array<int, string>|null $name = null) Unset the given store instances
 * @method static \Honed\Table\ViewManager forgetDrivers() Forget all of the resolved store instances
 * @method static \Honed\Table\ViewManager extend(string $driver, \Closure $callback) Register a custom driver creator Closure
 * @method static string serializeScope(mixed $scope) Serialize the given scope for storage
 * @method static string serializeTable(mixed $table) Get the table name for the given table
 * @method static \Honed\Table\ViewManager useMorphMap(bool $value = true) Specify that the Eloquent morph map should be used when serializing
 * @method static bool usesMorphMap() Determine if the Eloquent morph map should be used when serializing
 * @method static void resolveScopeUsing(callable $resolver) Set the default scope resolver
 * @method static callable defaultScopeResolver(string $driver) Get the default scope resolver
 * @method static \Honed\Table\PendingViewInteraction for(mixed|array<int, mixed> $scope = null) Create a pending view retrieval
 * @method static object|null get(string|\Honed\Table\Table $table, string $name, mixed $scope) Retrieve the view for the given table, name, and scope from storage
 * @method static array<int, object> list(mixed $table, array<int, mixed> $scopes) Retrieve the views for the given table and scopes from storage
 * @method static array<int, object> stored(mixed|array<int, mixed> $table) Get the views stored for a given table or tables
 * @method static array<int, object> scoped(mixed|array<int, mixed> $scope) Get the views stored for a given scope or scopes
 * @method static void set(mixed $table, string $name, mixed $scope, array<string, mixed> $view) Set the view for the given table and scope
 * @method static void delete(mixed $table, string $name, mixed $scope) Delete the view for the given table and scope from storage
 * @method static void purge(mixed|array<int, mixed>|null $table = null) Purge all views for the given table
 * @method static \Honed\Table\Contracts\Driver getDriver() Get the underlying view driver
 *
 * @see ViewManager
 */
class Views extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return ViewManager
     */
    public static function getFacadeRoot()
    {
        // @phpstan-ignore-next-line
        return parent::getFacadeRoot();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ViewManager::class;
    }
}
