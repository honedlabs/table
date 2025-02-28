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
     * @param  bool  $searchable
     * @return $this
     */
    public function searchable($searchable = true)
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * Determine if the column is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return $this->searchable;
    }
}
