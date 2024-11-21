<?php

namespace Honed\Table\Concerns\Search;

trait HasSearch
{
    /**
     * @var string|array<int, string>|null
     */
    protected $search;

    /**
     * Set the key to use as query parameter for searching.
     * 
     * @param string|array<int, string>|null $search
     * @return void
     */
    protected function setSearch($search): void
    {
        if (is_null($search)) {
            return;
        }

        $this->search = $search;
    }

    /**
     * Get the columns to search for.
     * 
     * @internal
     * @return string|array<int, string>
     */
    protected function definedSearch()
    {
        if (isset($this->search)) {
            return $this->search;
        }

        if (method_exists($this, 'search')) {
            return $this->search();
        }

        return config('table.search.columns', []);
    }

    /**
     * Get the columns to search for.
     * 
     * @return array<int, string>
     */
    public function getSearch()
    {
        return is_array($searches = $this->definedSearch()) ? $searches : [$searches];
    }
}
