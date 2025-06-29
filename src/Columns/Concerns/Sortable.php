<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;
use Honed\Refine\Sorts\Sort;

trait Sortable
{
    /**
     * The sortable of the instance.
     *
     * @var bool|string|Closure
     */
    protected $sortable = false;

    /**
     * The sort instance.
     *
     * @var Sort|null
     */
    protected $sort;

    /**
     * Set the instance to be sortable.
     *
     * @param  bool|string|Closure  $value
     * @return $this
     */
    public function sortable($value = true)
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Set the instance to not be sortable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notSortable($value = true)
    {
        return $this->sortable(! $value);
    }

    /**
     * Determine if the instance is sortable.
     *
     * @return bool
     */
    public function isSortable()
    {
        return (bool) $this->sortable;
    }

    /**
     * Determine if the instance is not sortable.
     *
     * @return bool
     */
    public function isNotSortable()
    {
        return ! $this->isSortable();
    }

    /**
     * Get the sort instance.
     *
     * @return Sort|null
     */
    public function getSort()
    {
        if (! $this->sortable) {
            return null;
        }

        return $this->sort ??= match (true) {
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
