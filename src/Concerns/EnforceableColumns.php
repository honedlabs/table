<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait EnforceableColumns
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $enforceColumns;

    /**
     * @var bool
     */
    protected static $globalEnforceColumns = false;

    /**
     * Enable enforce columns globally for all tables.
     *
     * @param  bool $enforceColumns
     */
    public static function enableEnforceColumns(bool $enforceColumns = true): void
    {
        static::$globalEnforceColumns = $enforceColumns;
    }

    /**
     * Determine if the table enforces columns.
     */
    public function enforcesColumns(): bool
    {
        return (bool) (value($this->inspect('enforceColumns', null)) ?? static::$globalEnforceColumns);
    }

    /**
     * Determine if the table does not enforce columns.
     */
    public function doesNotEnforceColumns(): bool
    {
        return ! $this->enforcesColumns();
    }
}
