<?php

declare(strict_types=1);

namespace Honed\Table;

use Closure;
use Honed\Table\Contracts\ViewScopeSerializeable;
use Honed\Table\Drivers\ArrayDriver;
use Honed\Table\Drivers\DatabaseDriver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;

class ViewManager
{
    /**
     * The container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The array of resolved view stores.
     *
     * @var array<string, Contracts\Driver>
     */
    protected $stores = [];

    /**
     * The registered custom driver creators.
     *
     * @var array<string, (Closure(string, Container): Contracts\Driver)>
     */
    protected $customCreators = [];

    /**
     * The default scope resolver.
     *
     * @var (Closure(string): mixed)|null
     */
    protected $defaultScopeResolver;

    /**
     * Indicates if the Eloquent "morph map" should be used when serializing.
     *
     * @var bool
     */
    protected $useMorphMap = false;

    /**
     * The default driver name.
     *
     * @var string
     */
    protected $defaultDriver = 'database';

    /**
     * Create a new view resolver.
     *
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Dynamically call the default store instance.
     *
     * @param  string  $method
     * @param  array<int, mixed>  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->store()->{$method}(...$parameters);
    }

    /**
     * Get a view store instance.
     *
     * @param  string|null  $store
     * @return Contracts\Driver
     *
     * @throws InvalidArgumentException
     */
    public function store($store = null)
    {
        return $this->driver($store);
    }

    /**
     * Get a view store instance by name.
     *
     * @param  string|null  $name
     * @return Contracts\Driver
     *
     * @throws InvalidArgumentException
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->stores[$name] = $this->get($name);
    }

    /**
     * Create an instance of the array driver.
     *
     * @param  string  $name
     * @return ArrayDriver
     */
    public function createArrayDriver($name)
    {
        return new ArrayDriver(
            $this->getDispatcher(), $name
        );
    }

    /**
     * Create an instance of the database driver.
     *
     * @param  string  $name
     * @return DatabaseDriver
     */
    public function createDatabaseDriver($name)
    {
        return new DatabaseDriver(
            $this->getDatabaseManager(), $this->getDispatcher(), $name
        );
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultDriver;
    }

    /**
     * Set the default driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->defaultDriver = $name;
    }

    /**
     * Forget all of the resolved store instances.
     *
     * @return $this
     */
    public function forgetDrivers()
    {
        $this->stores = [];

        return $this;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param  Closure(string, Container): Contracts\Driver  $callback
     * @return $this
     */
    public function extend($driver, $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Serialize the given scope for storage.
     *
     * @param  mixed  $scope
     * @return string
     *
     * @throws RuntimeException
     */
    public function serializeScope($scope)
    {
        return match (true) {
            $scope instanceof ViewScopeSerializeable => $scope->viewScopeSerialize(),
            $scope === null => '__laravel_null',
            is_string($scope) => $scope,
            is_numeric($scope) => (string) $scope,
            $scope instanceof Model
                && $this->useMorphMap => $scope->getMorphClass().'|'.(string) $scope->getKey(), // @phpstan-ignore cast.string
            $scope instanceof Model
                && ! $this->useMorphMap => $scope::class.'|'.(string) $scope->getKey(), // @phpstan-ignore cast.string
            default => throw new RuntimeException(
                'Unable to serialize the view scope to a string. You should implement the ViewScopeSerializeable contract.'
            )
        };
    }

    /**
     * Get the scope for the given table.
     *
     * @param  string|Table  $table
     * @return string
     */
    public function serializeTable($table)
    {
        return match (true) {
            $table instanceof Table => $table::class,
            default => $table,
        };
    }

    /**
     * Specify that the Eloquent morph map should be used when serializing.
     *
     * @param  bool  $value
     * @return $this
     */
    public function useMorphMap($value = true)
    {
        $this->useMorphMap = $value;

        return $this;
    }

    /**
     * Set the default scope resolver.
     *
     * @param  (Closure(string): mixed)  $resolver
     * @return void
     */
    public function resolveScopeUsing($resolver)
    {
        $this->defaultScopeResolver = $resolver;
    }

    /**
     * Create a pending view retrieval.
     *
     * @param  mixed|array<int, mixed>  $scope
     * @return PendingViewInteraction
     */
    public function for($scope = null)
    {
        return (new PendingViewInteraction($this->store()))->for($scope);
    }

    /**
     * Attempt to get the store from the local cache.
     *
     * @param  string  $name
     * @return Contracts\Driver
     *
     * @throws InvalidArgumentException
     */
    protected function get($name)
    {
        return $this->stores[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve a view store instance.
     *
     * @param  string  $name
     * @return Contracts\Driver
     *
     * @throws InvalidArgumentException
     */
    protected function resolve($name)
    {
        if (isset($this->customCreators[$name])) {
            return $this->callCustomCreator($name);
        }

        $method = 'create'.ucfirst($name).'Driver';

        if (method_exists($this, $method)) {
            /** @var Contracts\Driver */
            return $this->{$method}($name);
        }

        throw new InvalidArgumentException(
            "Driver [{$name}] not supported."
        );
    }

    /**
     * Call a custom driver creator.
     *
     * @param  string  $name
     * @return Contracts\Driver
     */
    protected function callCustomCreator($name)
    {
        return $this->customCreators[$name]($name, $this->container);
    }

    /**
     * The default scope resolver.
     *
     * @param  string  $driver
     * @return Closure(): mixed
     */
    protected function defaultScopeResolver($driver)
    {
        return function () use ($driver) {
            if ($this->defaultScopeResolver !== null) {
                return ($this->defaultScopeResolver)($driver);
            }

            // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible
            return $this->container['auth']->guard()->user();
        };
    }

    /**
     * Get the database manager instance from the container.
     *
     * @return DatabaseManager
     */
    protected function getDatabaseManager()
    {
        /** @var DatabaseManager */
        return $this->container['db']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }

    /**
     * Get the event dispatcher instance from the container.
     *
     * @return Dispatcher
     */
    protected function getDispatcher()
    {
        /** @var Dispatcher */
        return $this->container['events']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }
}
