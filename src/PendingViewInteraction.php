<?php

declare(strict_types=1);

namespace Honed\Table;

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
     * Load the pending view interaction for the given table.
     *
     * @param  mixed  $table
     * @return array<int, object>
     */
    public function load($table)
    {
        return $this->driver->list($table, $this->scope);
    }
}
