<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;
use Honed\Refine\Filters\Filter;
use Honed\Table\Columns\Column;

/**
 * @phpstan-require-extends \Honed\Table\Columns\Column
 */
trait Filterable
{
    /**
     * The filterable state of the column.
     *
     * @var bool|Closure
     */
    protected $filterable = false;

    /**
     * Set the instance to be filterable.
     *
     * @param  bool|Closure  $value
     * @return $this
     */
    public function filterable($value = true)
    {
        $this->filterable = $value;

        return $this;
    }

    /**
     * Set the instance to not be filterable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notFilterable($value = true)
    {
        return $this->filterable(! $value);
    }

    /**
     * Determine if the column is filterable.
     *
     * @return bool
     */
    public function isFilterable()
    {
        return (bool) $this->filterable;
    }

    /**
     * Determine if the column is not filterable.
     *
     * @return bool
     */
    public function isNotFilterable()
    {
        return ! $this->isFilterable();
    }

    /**
     * Get the filterable state of the column.
     *
     * @return Filter|null
     */
    public function getFilter()
    {
        if (! $this->filterable) {
            return null;
        }

        return match (true) {
            $this->filterable instanceof Closure => $this->newFilter()->query($this->filterable),

            default => $this->newFilter()
        };
    }

    /**
     * Create a new filter instance.
     *
     * @return Filter
     */
    protected function newFilter()
    {
        return Filter::make($this->getName(), $this->getLabel())
            ->alias($this->getAlias())
            ->as($this->getFilterableType())
            ->qualify($this->getQualifier());
    }

    /**
     * Get the filter interpreter type.
     *
     * @return 'string'|'array'|'boolean'|'int'|'date'|'datetime'|'time'|null
     */
    protected function getFilterableType()
    {
        return match ($this->getType()) {
            Column::ARRAY => 'array',
            Column::BOOLEAN => 'boolean',
            Column::DATE => 'date',
            Column::DATETIME => 'datetime',
            Column::TIME => 'time',
            Column::NUMERIC => 'int',
            Column::TEXT => 'string',
            default => null,
        };
    }
}
