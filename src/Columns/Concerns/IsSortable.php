<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Honed\Refine\Sort;

/**
 * @mixin \Honed\Core\Concerns\HasName
 */
trait IsSortable
{
    /**
     * @var bool|string
     */
    protected $sortable = false;

    /**
     * @var \Honed\Refine\Sort|null
     */
    protected $sort;

    /**
     * Set the column as sortable.
     *
     * @param  bool|string  $sortable
     * @return $this
     */
    public function sortable($sortable = true)
    {
        if (! $sortable) {
            return $this->disableSorting();
        }

        return $this->enableSorting($sortable);
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
     * Get the sort instance.
     *
     * @return \Honed\Refine\Sort|null
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Get the sort instance as an array.
     * 
     * @return array{direction: 'asc'|'desc'|null, next: string|null}
     */
    public function sortToArray()
    {
        return [
            'direction' => $this->getSort()?->getDirection(),
            'next' => $this->getSort()?->getNextDirection(),
        ];
    }

    /**
     * Disable sorting for the column.
     *
     * @return $this
     */
    protected function disableSorting()
    {
        $this->sortable = false;
        $this->sort = null;

        return $this;
    }

    /**
     * Enable sorting for the column.
     *
     * @param  string|bool  $sortable
     * @return $this
     */
    protected function enableSorting($sortable)
    {
        $this->sortable = true;
        $sortColumn = \is_string($sortable) ? $sortable : $this->getName();

        $this->sort = Sort::make($sortColumn)
            ->alias($this->getParameter());

        return $this;
    }
}
