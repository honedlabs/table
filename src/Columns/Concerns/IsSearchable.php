<?php

declare(strict_types=1);

namespace Conquest\Table\Columns\Concerns;

use Closure;

trait IsSearchable
{
    /**
     * @var bool|Closure
     */
    protected $searchable = false;

    /**
     * @var string|Closure|null
     */
    protected $searchProperty = null;

    /**
     * Set the searchable property.
     * 
     * @param string|\Closure $property
     * @return $this
     */
    public function searchable($property = null)
    {
        $this->setSearchable(true);
        $this->setSearchProperty($property);

        return $this;
    }

    /**
     * Set the searchable property quietly.
     * 
     * @param bool|\Closure|null $searchable
     * @return void
     */
    public function setSearchable($searchable)
    {
        if (is_null($searchable)) {
            return;
        }
        $this->searchable = $searchable;
    }

    /**
     * Set the search property quietly.
     * 
     * @param string|\Closure|null $property
     * @return void
     */
    public function setSearchProperty($property)
    {
        if (is_null($property)) {
            return;
        }
        $this->searchProperty = $property;
    }

    /**
     * Determine if the column is searchable.
     * 
     * @return bool
     */
    public function isSearchable()
    {
        return $this->evaluate($this->searchable);
    }

    /**
     * Determine if the column is not searchable.
     * 
     * @return bool
     */
    public function isNotSearchable()
    {
        return ! $this->isSearchable();
    }

    /**
     * Get the search property.
     * 
     * @return string|\Closure|null
     */
    public function getSearchProperty()
    {
        return $this->evaluate($this->searchProperty);
    }

    /**
     * Determine if the column lacks a search property.
     * 
     * @return bool
     */
    public function lacksSearchProperty()
    {
        return is_null($this->searchProperty);
    }

    /**
     * Determine if the column has a search property.
     * 
     * @return bool
     */
    public function hasSearchProperty()
    {
        return ! $this->lacksSearchProperty();
    }
}
