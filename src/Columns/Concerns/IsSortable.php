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
        return (bool) $this->sortable;
    }

    /**
     * Get the sort instance.
     */
    public function getSort(): ?Sort
    {
        return $this->sort;
    }

    /**
     * @return array{direction: 'asc'|'desc'|null, next: string|null}
     */
    public function sortToArray(): array
    {
        return [
            'direction' => $this->getSort()?->getDirection(),
            'next' => $this->getSort()?->getNextDirection(),
        ];
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
    protected function enableSorting(string|true $sortable): static
    {
        $this->sortable = true;
        $this->sort = Sort::make(
            \is_string($sortable)
                ? $sortable : type($this->getName())->asString()
        );

        return $this;
    }
}
