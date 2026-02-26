<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;
use Honed\Refine\Filters\Filter;

/**
 * @phpstan-require-extends \Honed\Table\Columns\Column
 */
trait Filterable
{
    /**
     * The filterable state of the column.
     *
     * @var bool|Closure|Filter
     */
    protected $filterable = false;

    /**
     * Set the instance to be filterable.
     *
     * @return $this
     */
    public function filterable(bool|Closure|Filter $value = true): static
    {
        $this->filterable = $value;

        return $this;
    }

    /**
     * Set the instance to not be filterable.
     *
     * @return $this
     */
    public function notFilterable(bool $value = true): static
    {
        return $this->filterable(! $value);
    }

    /**
     * Determine if the column is filterable.
     */
    public function isFilterable(): bool
    {
        return (bool) $this->filterable;
    }

    /**
     * Determine if the column is not filterable.
     */
    public function isNotFilterable(): bool
    {
        return ! $this->isFilterable();
    }

    /**
     * Get the filterable state of the column.
     */
    public function getFilter(): ?Filter
    {
        if (! $this->filterable) {
            return null;
        }

        return match (true) {
            $this->filterable instanceof Closure => $this->newFilter()->query($this->filterable),
            $this->filterable instanceof Filter => $this->filterable,
            default => $this->newFilter()
        };
    }

    /**
     * Create a new filter instance.
     */
    protected function newFilter(): Filter
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
    protected function getFilterableType(): ?string
    {
        return match ($this->getType()) {
            'array' => 'array',
            'boolean' => 'boolean',
            'date' => 'date',
            'datetime' => 'datetime',
            'time' => 'time',
            'numeric' => 'int',
            'text' => 'string',
            default => null,
        };
    }
}
