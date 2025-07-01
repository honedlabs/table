<?php

declare(strict_types=1);

namespace Honed\Table;

use Closure;
use Honed\Table\Contracts\Driver;
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
     * @var array<string, Closure(string, Container): Driver>
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
     *
     * @throws InvalidArgumentException
     */
    public function store(?string $store = null): Decorator
    {
        return $this->driver($store);
    }

    /**
     * Get a view store instance by name.
     *
     *
     * @throws InvalidArgumentException
     */
    public function driver(?string $name = null): Decorator
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->stores[$name] = $this->cached($name);
    }

    /**
     * Create an instance of the array driver.
     */
    public function createArrayDriver(string $name): ArrayDriver
    {
        return new ArrayDriver(
            $this->getDispatcher(), $name
        );
    }

    /**
     * Create an instance of the database driver.
     */
    public function createDatabaseDriver(string $name): DatabaseDriver
    {
        return new DatabaseDriver(
            $this->getDatabaseManager(), $this->getDispatcher(), $name
        );
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        // @phpstan-ignore-next-line offsetAccess.nonOffsetAccessible
        return $this->container['config']->get('table.views.driver', 'database');
    }

    /**
     * Set the default driver name.
     */
    public function setDefaultDriver(string $name): void
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
    public function forgetDriver(string|array|null $name = null): static
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
    public function forgetDrivers(): static
    {
        $this->stores = [];

        return $this;
    }

    /**
     * Register a custom driver creator closure.
     *
     * @param  Closure(string, Container): Driver  $callback
     * @return $this
     */
    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * Serialize the given scope for storage.
     *
     *
     * @throws RuntimeException
     */
    public function serializeScope(mixed $scope): string
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
     */
    public function serializeTable(mixed $table): string
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
     * @return $this
     */
    public function useMorphMap(bool $value = true): static
    {
        $this->useMorphMap = $value;

        return $this;
    }

    /**
     * Determine if the Eloquent morph map should be used when serializing.
     */
    public function usesMorphMap(): bool
    {
        return $this->useMorphMap;
    }

    /**
     * Set the default scope resolver.
     *
     * @param  (callable(): mixed)  $resolver
     */
    public function resolveScopeUsing(callable $resolver): void
    {
        $this->defaultScopeResolver = $resolver;
    }

    /**
     * The default scope resolver.
     *
     * @return callable(): mixed
     */
    public function defaultScopeResolver(string $driver): callable
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
     *
     * @throws InvalidArgumentException
     */
    protected function cached(string $name): Decorator
    {
        return $this->stores[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve a view store instance.
     *
     *
     * @throws InvalidArgumentException
     */
    protected function resolve(string $name): Decorator
    {
        if (isset($this->customCreators[$name])) {
            $driver = $this->callCustomCreator($name);
        } else {
            $method = 'create'.ucfirst($name).'Driver';

            if (method_exists($this, $method)) {
                /** @var Driver */
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
     */
    protected function callCustomCreator(string $name): Driver
    {
        return $this->customCreators[$name]($name, $this->container);
    }

    /**
     * Get the database manager instance from the container.
     */
    protected function getDatabaseManager(): DatabaseManager
    {
        /** @var DatabaseManager */
        return $this->container['db']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }

    /**
     * Get the event dispatcher instance from the container.
     */
    protected function getDispatcher(): Dispatcher
    {
        /** @var Dispatcher */
        return $this->container['events']; // @phpstan-ignore-line offsetAccess.nonOffsetAccessible
    }
}
