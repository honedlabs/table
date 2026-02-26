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
     * @var bool|string|Closure|Sort
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
     * @return $this
     */
    public function sortable(bool|string|Closure|Sort $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Set the instance to not be sortable.
     *
     * @return $this
     */
    public function notSortable(bool $value = true): static
    {
        return $this->sortable(! $value);
    }

    /**
     * Determine if the instance is sortable.
     */
    public function isSortable(): bool
    {
        return (bool) $this->sortable;
    }

    /**
     * Determine if the instance is not sortable.
     */
    public function isNotSortable(): bool
    {
        return ! $this->isSortable();
    }

    /**
     * Get the sort instance.
     */
    public function getSort(): ?Sort
    {
        if (! $this->sortable) {
            return null;
        }

        return $this->sort ??= match (true) {
            $this->sortable instanceof Closure => $this->newSort()->query($this->sortable),
            $this->sortable instanceof Sort => $this->sortable,
            is_string($this->sortable) => $this->newSort($this->sortable),
            default => $this->newSort()
        };
    }

    /**
     * Get the sort instance as an array.
     *
     * @return array<string,mixed>|null
     */
    public function sortToArray(): ?array
    {
        $sort = $this->getSort();

        if (! $sort) {
            return null;
        }

        return [
            'active' => $sort->isActive(),
            'direction' => $sort->getDirection(),
            'next' => $sort->getNextDirection(),
        ];
    }

    /**
     * Create a new sort instance.
     */
    protected function newSort(?string $name = null): Sort
    {
        /** @var string */
        $name = $name ?? $this->getName();

        return Sort::make($name, $this->getLabel())
            ->hidden()
            ->alias($this->getAlias())
            ->qualify($this->getQualifier());
    }
}
