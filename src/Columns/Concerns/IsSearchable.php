<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsSearchable
{
    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * Set as searchable, chainable.
     *
     * @return $this
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * Determine if it is searchable.
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }
}
