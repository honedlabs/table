<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;
use Honed\Refine\Searches\Search;

trait Searchable
{
    /**
     * The searchable of the instance.
     *
     * @var bool|string|Closure
     */
    protected $searchable = false;

    /**
     * Set the instance to be searchable.
     *
     * @param  bool|string|Closure  $value
     * @return $this
     */
    public function searchable($value = true)
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Set the instance to not be searchable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notSearchable($value = true)
    {
        return $this->searchable(! $value);
    }

    /**
     * Determine if the instance is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return (bool) $this->searchable;
    }

    /**
     * Determine if the instance is not searchable.
     *
     * @return bool
     */
    public function isNotSearchable()
    {
        return ! $this->isSearchable();
    }

    /**
     * Get the search instance.
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
