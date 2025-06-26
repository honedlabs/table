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
     * Set the filterable state of the column.
     *
     * @param  bool|Closure  $filterable
     * @return $this
     */
    public function filterable($filterable = true)
    {
        $this->filterable = $filterable;

        return $this;
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
