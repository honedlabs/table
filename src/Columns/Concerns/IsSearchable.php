<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsSearchable
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $searchable = false;

    /**
     * Set the searchable property, chainable.
     *
     * @param  bool|(\Closure():bool)  $searchable
     * @return $this
     */
    public function searchable(bool|\Closure $searchable = true): static
    {
        $this->setSearchable($searchable);

        return $this;
    }

    /**
     * Set the searchable property quietly.
     *
     * @param  bool|(\Closure():bool)|null $searchable
     */
    public function setSearchable(bool|\Closure|null $searchable): void
    {
        if (\is_null($searchable)) {
            return;
        }
        $this->searchable = $searchable;
    }

    /**
     * Determine if the column is searchable.
     */
    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->searchable);
    }

    /**
     * Determine if the column is not searchable.
     */
    public function isNotSearchable(): bool
    {
        return ! $this->isSearchable();
    }
}
