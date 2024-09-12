<?php

namespace Conquest\Table\Concerns;

trait HasSearchAs
{
    /**
     * @var string
     */
    protected $searchAs;

    /**
     * Set the key to use as query parameter for searching.
     *
     * @param  string|null  $searchAs
     */
    protected function setSearchAs($searchAs): void
    {
        if (is_null($searchAs)) {
            return;
        }

        $this->searchAs = $searchAs;
    }

    /**
     * Get the searchAs key to use.
     *
     * @internal
     *
     * @return string
     */
    protected function getSearchAs()
    {
        if (isset($this->searchAs)) {
            return $this->searchAs;
        }

        if (method_exists($this, 'searchAs')) {
            return $this->searchAs();
        }

        return config('table.search.search', 'q');
    }

    /**
     * Get the searchAs direction from the request query parameters.
     *
     * @internal
     *
     * @return string|null
     */
    protected function getSearchFromRequest()
    {
        return request()->string($this->getSearchAs(), null);
    }
}
