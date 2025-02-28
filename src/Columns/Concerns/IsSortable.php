<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Honed\Refine\Sorts\Sort;

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
     * @var \Honed\Refine\Sorts\Sort|null
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
     * @return \Honed\Refine\Sorts\Sort|null
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
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
        $this->sort = Sort::make(
            \is_string($sortable)
                ? $sortable : type($this->getName())->asString()
        );

        return $this;
    }
}
