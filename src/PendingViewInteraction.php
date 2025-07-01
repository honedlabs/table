<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Table\Drivers\Decorator;
use Honed\Table\Facades\Views;
use RuntimeException;

class PendingViewInteraction
{
    /**
     * The view driver.
     *
     * @var Decorator
     */
    protected $driver;

    /**
     * The feature interaction scope.
     *
     * @var mixed
     */
    protected $scope;

    /**
     * Create a new pending view interaction.
     */
    public function __construct(Decorator $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Set the scope for the pending view interaction.
     *
     * @return $this
     */
    public function for(mixed $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get the scope for the pending view interaction.
     */
    public function getScope(): mixed
    {
        return $this->scope;
    }

    /**
     * Get the underlying driver for the interaction.
     */
    public function getDriver(): Decorator
    {
        return $this->driver;
    }

    /**
     * Retrieve the view for the given table and name, and scopes.
     *
     * @return object|null
     */
    public function get(mixed $table, string $name)
    {
        return $this->driver->get($table, $name, $this->getScope());
    }

    /**
     * Retrieve the views for the given table.
     *
     * @return array<int, object>
     */
    public function list(mixed $table): array
    {
        return $this->driver->list($table, $this->getScope());
    }

    /**
     * Get the views stored for the given table.
     *
     * @return array<int, object>
     *
     * @throws RuntimeException
     */
    public function stored(mixed $table): array
    {
        return $this->driver->stored($table);
    }

    /**
     * Get the views stored.
     *
     * @return array<int, object>
     *
     * @throws RuntimeException
     */
    public function scoped(): array
    {
        return $this->driver->scoped($this->getScope());
    }

    /**
     * Create a new view for the given table and name.
     *
     * @param  array<string, mixed>  $view
     */
    public function create(mixed $table, string $name, array $view): void
    {
        $this->driver->create($table, $name, $this->getScope(), $view);
    }

    /**
     * Set the view for the given table and name.
     *
     * @param  array<string, mixed>  $view
     */
    public function set(mixed $table, string $name, array $view): void
    {
        $this->driver->set($table, $name, $this->getScope(), $view);
    }

    /**
     * Delete the view for the given table and name.
     */
    public function delete(mixed $table, string $name): void
    {
        $this->driver->delete($table, $name, $this->getScope());
    }
}
