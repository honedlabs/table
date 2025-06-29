<?php

declare(strict_types=1);

namespace Honed\Table;

use RuntimeException;

class PendingViewInteraction
{
    /**
     * The view driver.
     *
     * @var Drivers\Decorator
     */
    protected $driver;

    /**
     * The feature interaction scope.
     *
     * @var array<int, mixed>
     */
    protected $scope = [];

    /**
     * Create a new pending view interaction.
     *
     * @param  Drivers\Decorator  $driver
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    /**
     * Set the scope for the pending view interaction.
     *
     * @param  mixed|array<int, mixed>  $scope
     * @return $this
     */
    public function for($scope)
    {
        $scope = is_array($scope) ? $scope : func_get_args();

        $this->scope = [...$this->scope, ...$scope];

        return $this;
    }

    /**
     * Get the scope for the pending view interaction.
     *
     * @return array<int, mixed>
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Get the underlying driver for the interaction.
     *
     * @return Drivers\Decorator
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Retrieve the views for the given table.
     *
     * @param  mixed  $table
     * @return array<int, object>
     */
    public function list($table)
    {
        return $this->driver->list($table, $this->getScope());
    }

    /**
     * Get the views stored for the given table.
     *
     * @param  mixed  $table
     * @return array<int, object>
     *
     * @throws RuntimeException
     */
    public function stored($table)
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
    public function scoped()
    {
        return $this->driver->scoped($this->getScope());
    }
}
