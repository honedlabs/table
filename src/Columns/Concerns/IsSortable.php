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
     * @param  \Honed\Refine\Sort|string|bool  $sortable
     * @param  string|null  $alias
     * @param  bool  $default
     * @return $this
     */
    public function sortable($sortable = true, $alias = null, $default = false)
    {
        if (! $sortable) {
            return $this->disableSorting();
        }

        return $this->enableSorting($sortable, $alias, $default);
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
            'active' => $this->getSort()?->isActive(),
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
     * @param  \Honed\Refine\Sort|string|bool  $sortable
     * @param  string|null  $alias
     * @param  bool  $default
     * @return $this
     */
    protected function enableSorting($sortable = true, $alias = null, $default = false)
    {
        $this->sortable = true;

        $this->sort = match (true) {
            $sortable instanceof Sort => $sortable,

            \is_string($sortable) => Sort::make($sortable)
                ->alias($alias ?? $this->getParameter())
                ->default($default),

            default => Sort::make($this->getName())
                ->alias($alias ?? $this->getParameter())
                ->default($default),
        };

        return $this;
    }
}
