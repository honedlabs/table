<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;
use Honed\Refine\Sorts\Sort;

trait Sortable
{
    /**
     * The sortable state of the column.
     *
     * @var bool|string|Closure
     */
    protected $sortable = false;

    /**
     * Set the sortable state of the column.
     *
     * @param  bool|string|Closure  $sortable
     * @return $this
     */
    public function sortable($sortable = true)
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * Determine if the column is sortable.
     *
     * @return bool
     */
    public function isSortable()
    {
        return (bool) $this->sortable;
    }

    /**
     * Get the sortable state of the column.
     *
     * @return Sort|null
     */
    public function getSort()
    {
        if (! $this->sortable) {
            return null;
        }

        return match (true) {
            $this->sortable instanceof Closure => $this->newSort()->query($this->sortable),

            is_string($this->sortable) => $this->newSort($this->sortable),

            default => $this->newSort()
        };
    }

    /**
     * Create a new sort instance.
     *
     * @param  string|null  $name
     * @return Sort
     */
    protected function newSort($name = null)
    {
        return Sort::make($name ?? $this->getName(), $this->getLabel())
            ->hidden()
            ->alias($this->getAlias())
            ->qualify($this->getQualifier());
    }
}
