<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

trait IsOptimizable
{
    /**
     * Whether to optimize the query `select` statement.
     * 
     * @var bool
     */
    protected $optimize;

    /**
     * @var bool
     */
    protected static $defaultOptimize = false;

    /**
     * Set whether all tables should be optimized by default.
     */
    public static function shouldOptimize(bool $optimize = true): void
    {
        static::$defaultOptimize = $optimize;
    }

    /**
     * Set whether the table should be optimized, quietly.
     */
    public function setOptimize(bool $optimize = true): void
    {
        $this->optimize = $optimize;
    }

    /**
     * Determine if the table should be optimized.
     */
    public function isOptimized(): bool
    {
        return \property_exists($this, 'optimize') && ! \is_null($this->optimize)
            ? $this->optimize
            : static::$defaultOptimize;
    }

    /**
     * Optimize a query
     * 
     * @param \Illuminate\Support\Collection<\Honed\Table\Columns\Contracts\Column> $activeColumns
     */
    public function optimize(Builder $builder, Collection $activeColumns): void
    {
        if (! $this->isOptimized()) {
            return;
        }

        $builder->select(...$activeColumns->map->getName());
    }
}
