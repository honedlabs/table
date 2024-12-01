<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Honed\Table\Sorts\ToggleSort;

/**
 * Assymetric definition
 */
trait IsSortable
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $sortable = false;

    /**
     * Set the sortable property, chainable.
     * 
     * @param bool|(\Closure():bool) $sortable
     * @return $this
     */
    public function sortable(bool|\Closure $sortable = true): static
    {
        $this->setSortable($sortable);

        return $this;
    }

    /**
     * Set the sortable property quietly.
     * 
     * @param bool|\Closure|null $sortable
     */
    public function setSortable(bool|\Closure|null $sortable): void
    {
        if (\is_null($sortable)) {
            return;
        }
        $this->sortable = $sortable;
    }

    /**
     * Determine if the column is sortable.
     * 
     * @return bool
     */
    public function isSortable(): bool
    {
        return (bool) $this->evaluate($this->sortable);
    }

    /**
     * Determine if the column is not sortable.
     * 
     * @return bool
     */
    public function isNotSortable(): bool
    {
        return ! $this->isSortable();
    }
}
