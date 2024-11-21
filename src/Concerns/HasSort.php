<?php

namespace Honed\Table\Concerns;

trait HasSort
{
    /**
     * @var string
     */
    protected $sort;

    /**
     * Set the key to use as query parameter for sorting.
     * 
     * @param string|null $sort
     * @return void
     */
    protected function setSort($sort): void
    {
        if (is_null($sort)) {
            return;
        }
        $this->sort = $sort;
    }

    /**
     * Get the sort key to use.
     * 
     * @internal
     * @return string
     */
    protected function definedSort()
    {
        if (isset($this->sort)) {
            return $this->sort;
        }

        if (method_exists($this, 'sort')) {
            return $this->sort();
        }

        return config('table.sorting.sort_key', 'sort');
    }

    /**
     * Get the active sort name.
     * 
     * @return string|null 
     */
    public function getSort()
    {
        return request()->input($this->definedSort(), null);
    }
}