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
     * @var \Honed\Table\Sorts\Contracts\Sort|null
     */
    protected $sort;

    /**
     * Set the column as sortable.
     *
     * @return $this
     */
    public function sortable(bool|string $sortable = true): static
    {
        if (! $sortable) {
            return $this->disableSorting();
        }

        return $this->enableSorting($sortable);
    }

    /**
     * Determine if the column is sortable.
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Get the sort instance.
     */
    public function getSort(): ?Sort
    {
        return $this->sort;
    }

    /**
     * @return $this
     */
    protected function disableSorting(): static
    {
        $this->sortable = false;
        $this->sort = null;

        return $this;
    }

    /**
     * @return $this
     */
    protected function enableSorting(string $sortable): static
    {
        $this->sortable = true;
        $this->sort = Sort::make(
            \is_string($sortable) ? $sortable : $this->getName()
        );

        return $this;
    }
}
