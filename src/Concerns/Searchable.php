<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

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
     * @var string|null
     */
    protected static $defaultSearchAs = 'q';

    /**
     * @var string
     */
    protected $searchAs;

    /**
     * @var bool
     */
    protected static $defaultScout = false;

    /**
     * @var bool
     */
    protected $scout;

    /**
     * Configure the default search query parameter to use for all tables.
     * 
     * @param string $search
     * @return void
     */
    public static function searchUsing(string $search)
    {
        static::$search = $search;
    }

    /**
     * Configure whether to use Laravel Scout for searching of tables.
     * 
     * @param bool $scout
     * @return void
     */
    public static function useScout(bool $scout = true)
    {
        static::$scout = $scout;
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
        return $this->inspect('searchAs', static::$defaultSearchAs);
    }

    /**
     * Determine whether to use Laravel Scout for searching.
     * 
     * @return bool
     */
    public function isScoutSearch()
    {
        return $this->inspect('scout', static::$defaultScout);
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
     * @param \Illuminate\Database\Query\Builder $builder
     * @return void
     */
    protected function applySearch($builder)
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