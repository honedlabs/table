<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

trait IsSearchable
{
    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * Set the column as searchable.
     *
     * @return $this
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * Determine if the column is searchable.
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
}
