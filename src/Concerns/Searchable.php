<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Honed\Core\Concerns\Inspectable
 */
trait Searchable
{
    /**
     * @var string|array
     */
    protected $search;

    /**
     * @var string
     */
    protected $searchAs;

    /**
     * @var string
     */
    protected static $globalSearchAs = 'search';

    /**
     * @var bool
     */
    protected $scout;

    /**
     * @var bool
     */
    protected static $globalScout = false;

    /**
     * Configure the default search query parameter to use for all tables.
     * 
     * @param string|array<int,string> $searchAs
     * @return void
     */
    public static function setSearchAs(string $searchAs)
    {
        static::$globalSearchAs = $searchAs;
    }

    /**
     * Configure whether to enable Laravel Scout for searching of all tables by default.
     * 
     * @param bool $scout
     * @return void
     */
    public static function enableScout(bool $scout = true)
    {
        static::$globalScout = $scout;
    }

    /**
     * Get the columns to use for searching.
     * 
     * @return string|array<int,string>|null
     */
    public function getSearch()
    {
        return $this->inspect('search', null);
    }

    /**
     * Get the query parameter needed to identify the search term.
     * 
     * @return string
     */
    public function getSearchAs()
    {
        return $this->inspect('searchAs', static::$globalSearchAs);
    }

    /**
     * Determine whether to use Laravel Scout for searching.
     * 
     * @return bool
     */
    public function isScoutSearch()
    {
        return $this->inspect('scout', static::$globalScout);
    }

    /**
     * Get the search term from the request query parameters.
     * 
     * @return string|null
     */
    public function getSearchTerm()
    {
        return request()->input($this->getSearchAs(), null);
    }

    /**
     * Determine whether to apply searching if available.
     * 
     * @return bool
     */
    public function searching()
    {
        return filled($this->getSearch()) && (bool) $this->getSearchTerm();
    }

    /**
     * Apply the search to the builder.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return void
     */
    protected function applySearch(Builder $builder)
    {
        if (! $this->searching()) {
            return;
        }

        if ($this->isScoutSearch()) {
            // @phpstan-ignore-next-line
            $builder->search($this->getSearchTerm());
            return;
        } 

        $builder->whereAny(
            $this->getSearch(),
            'LIKE',
            "%{$this->getSearchTerm()}%"
        );
    }
}