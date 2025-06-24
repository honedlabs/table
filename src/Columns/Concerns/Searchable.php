<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;
use Honed\Refine\Searches\Search;

trait Searchable
{
    /**
     * The searchable state of the column.
     *
     * @var bool|string|Closure
     */
    protected $searchable = false;

    /**
     * Set the searchable state of the column.
     *
     * @param  bool|string|Closure  $searches
     * @return $this
     */
    public function searchable($searches = true)
    {
        $this->searchable = $searches;

        return $this;
    }

    /**
     * Determine if the column is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return (bool) $this->searchable;
    }

    /**
     * Get the columns to search on.
     *
     * @return Search|null
     */
    public function getSearch()
    {
        if (! $this->searchable) {
            return null;
        }

        return match (true) {
            $this->searchable instanceof Closure => $this->newSearch()->query($this->searchable),

            is_string($this->searchable) => $this->newSearch($this->searchable),

            default => $this->newSearch()
        };
    }

    /**
     * Create a new search instance.
     *
     * @param  string|null  $name
     * @return Search
     */
    protected function newSearch($name = null)
    {
        return Search::make($name ?? $this->getName(), $this->getLabel())
            ->alias($this->getAlias())
            ->qualify($this->getQualifier());
    }
}
