<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Honed\Core\Concerns\Inspectable
 */
trait Searchable
{
    /**
     * @var string|array<int,string>
     */
    protected $search;

    /**
     * @var string
     */
    protected $searchName;

    /**
     * @var string
     */
    protected static $useSearchName = 'search';

    /**
     * @var bool
     */
    protected $scout;

    /**
     * @var bool
     */
    protected static $useScout = false;

    /**
     * Configure the default search query parameter to use for all tables.
     *
     * @param  string  $search
     * @return void
     */
    public static function useSearchName(string $search)
    {
        static::$useSearchName = $search;
    }

    /**
     * Get the default search query parameter name.
     *
     * @return string
     */
    public static function getDefaultSearchName(): string
    {
        return static::$useSearchName;
    }

    /**
     * Configure whether to enable Laravel Scout for searching of all tables by default.
     *
     * @return void
     */
    public static function useScout(bool $scout = true)
    {
        static::$useScout = $scout;
    }

    /**
     * Determine whether to use Laravel Scout for searching.
     *
     * @return bool
     */
    public static function usesScout(): bool
    {
        return static::$useScout;
    }

    /**
     * Get the columns to use for searching.
     *
     * @return string|array<int,string>
     */
    public function getSearch(): string|array
    {
        return $this->inspect('search', []);
    }

    /**
     * Get the query parameter needed to identify the search term.
     *
     * @return string
     */
    public function getSearchName()
    {
        return $this->inspect('searchName', static::getDefaultSearchName());
    }

    /**
     * Determine whether to use Laravel Scout for searching.
     *
     * @return bool
     */
    public function isScoutSearch()
    {
        return $this->inspect('scout', static::usesScout());
    }

    /**
     * Get the search term from the request query parameters.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return string|null
     */
    public function getSearchParameters(Request $request = null)
    {
        $request = $request ?? request();

        return $request->input($this->getSearchAs(), null);
    }

    /**
     * Determine whether to apply searching if available.
     *
     * @return bool
     */
    public function isSearching()
    {
        return filled($this->getSearch()) && (bool) $this->getSearchParameters();
    }

    /**
     * Apply the search to the builder.
     *
     * @return void
     */
    protected function searchQuery(Builder $builder)
    {
        if (! $this->isSearching()) {
            return;
        }

        $term = $this->getSearchParameters();

        if ($this->isScoutSearch()) {
            // @phpstan-ignore-next-line
            $builder->search($term);

            return;
        }

        $builder->whereAny(
            $this->getSearch(),
            'LIKE',
            "%{$term}%"
        );
    }
}
