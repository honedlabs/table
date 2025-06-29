<?php

declare(strict_types=1);

namespace Honed\Table;

use Closure;
use Honed\Table\Contracts\ViewScopeSerializeable;
use Honed\Table\Drivers\ArrayDriver;
use Honed\Table\Drivers\DatabaseDriver;
use Honed\Table\Drivers\Decorator;
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
     * @var array<string, Decorator>
     */
    protected $stores = [];

    /**
     * The registered custom driver creators.
     *
     * @var array<string, Closure(string, Container): Contracts\Driver>
     */
    protected $customCreators = [];

    /**
     * The default scope resolver.
     *
     * @var (callable(mixed...): mixed)|null
     */
    protected $defaultScopeResolver;

    /**
     * Indicates if the Eloquent "morph map" should be used when serializing.
     *
     * @var bool
     */
    protected $useMorphMap = false;

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
     * @return Decorator
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
     * @return Decorator
     *
     * @throws InvalidArgumentException
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->stores[$name] = $this->cached($name);
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
        // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible
        return $this->container['config']->get('table.views.driver', 'database');
    }

    /**
     * Set the default driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible
        $this->container['config']->set('table.views.driver', $name);
    }

    /**
     * Unset the given store instances.
     *
     * @param  string|array<int, string>|null  $name
     * @return $this
     */
    public function forgetDriver($name = null)
    {
        $name ??= $this->getDefaultDriver();

        foreach ((array) $name as $storeName) {
            if (isset($this->stores[$storeName])) {
                unset($this->stores[$storeName]);
            }
        }

        return $this;
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
     * Register a custom driver creator closure.
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
                && $this->usesMorphMap() =>
                    // @phpstan-ignore-next-line cast.string
                    $scope->getMorphClass().'|'.(string) $scope->getKey(),
            $scope instanceof Model =>
                    // @phpstan-ignore-next-line cast.string
                    $scope::class.'|'.(string) $scope->getKey(),
            default => throw new RuntimeException(
                'Unable to serialize the view scope to a string. You should implement the ViewScopeSerializeable contract.'
            )
        };
    }

    /**
     * Get the scope for the given table.
     *
     * @param  mixed  $table
     * @return string
     */
    public function serializeTable($table)
    {
        return match (true) {
            $table === null => '__laravel_null',
            $table instanceof Table => $table::class,
            is_string($table) => $table,
            default => throw new RuntimeException(
                'Unable to serialize the provided value to a table scope.'
            )
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
     * Determine if the Eloquent morph map should be used when serializing.
     *
     * @return bool
     */
    public function usesMorphMap()
    {
        return $this->useMorphMap;
    }

    /**
     * Set the default scope resolver.
     *
     * @param  (callable(): mixed)  $resolver
     * @return void
     */
    public function resolveScopeUsing($resolver)
    {
        $this->defaultScopeResolver = $resolver;
    }

    /**
     * The default scope resolver.
     *
     * @param  string  $driver
     * @return callable(): mixed
     */
    public function defaultScopeResolver($driver)
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
     * Attempt to get the store from the local cache.
     *
     * @param  string  $name
     * @return Decorator
     *
     * @throws InvalidArgumentException
     */
    protected function cached($name)
    {
        return $this->stores[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve a view store instance.
     *
     * @param  string  $name
     * @return Decorator
     *
     * @throws InvalidArgumentException
     */
    protected function resolve($name)
    {
        if (isset($this->customCreators[$name])) {
            $driver = $this->callCustomCreator($name);
        } else {
            $method = 'create'.ucfirst($name).'Driver';

            if (method_exists($this, $method)) {
                /** @var Contracts\Driver */
                $driver = $this->{$method}($name);
            } else {
                throw new InvalidArgumentException(
                    "Driver [{$name}] not supported."
                );
            }
        }

        return new Decorator(
            $name,
            $driver,
            $this->defaultScopeResolver($name)
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
