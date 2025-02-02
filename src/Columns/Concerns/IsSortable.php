<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Honed\Table\Sorts\Sort;

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
     * Set as sortable, chainable.
     *
     * @return $this
     */
    public function sortable(bool|string|null $sortable = true): static
    {
        $this->setSortable($sortable);

        return $this;
    }

    /**
     * Set as sortable quietly.
     */
    public function setSortable(bool|string|null $sortable): void
    {
        if (\is_null($sortable)) {
            return;
        }

        $sortName = \is_string($sortable) 
            ? $sortable 
            : $this->getName();

        $this->sort = Sort::make($sortName)->agnostic();
        $this->sortable = (bool) $sortable;
    }

    /**
     * Determine if it is sortable.
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
}
