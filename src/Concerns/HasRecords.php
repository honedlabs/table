<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Honed\Table\Exceptions\InvalidPaginatorException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait HasRecords
{
    /**
     * The records of the table retrieved from the resource.
     * 
     * @var \Illuminate\Support\Collection<array-key,array<array-key,mixed>>|null
     */
    protected $records = null;

        /**
     * The number of records to show per page. 
     * An array provides options allowing users to change the number of records shown to themper page.
     * 
     * @var int|array<int,int>
     */
    protected $perPage;

    /**
     * The default number of records to show per page.
     * If $perPage is an array, this should be one of the values.
     * If not supplied, the lowest value in $perPage will be used.
     * 
     * @var int
     */
    protected $defaultPerPage;

    /**
     * The number of records to use per page for all tables.
     * 
     * @var int|array<int,int>
     */
    protected static $defaultPerPageAmount = 10;

    /**
     * The paginator instance to use for the table.
     * 
     * @var class-string|null
     */
    protected $paginator;

    /**
     * The paginator type to use for all tables.
     * 
     * @var class-string|null
     */
    protected static $defaultPaginator = LengthAwarePaginator::class;

    /**
     * The name to use for the page query parameter.
     * 
     * @var string
     */
    protected $page;

    /**
     * The name to use for the page query parameter for all tables.
     * 
     * @var string|null
     */
    protected static $pageKey = null;

    /**
     * The name to use for changing the number of records per page.
     * 
     * @var string
     */
    protected $count;

    /**
     * The name to use for changing the number of records per page for all tables.
     * 
     * @var string
     */
    protected static $countKey = 'show';

    /**
     * Configure the options for the number of items to show per page.
     *
     * @param  int|array<int,int>  $perPage
     * @return void
     */
    public static function recordsPerPage(int|array $perPage)
    {
        static::$defaultPerPageAmount = $perPage;
    }

    /**
     * Configure the default paginator to use.
     *
     * @param  string|\Honed\Table\Enums\Paginator  $paginator
     * @return void
     */
    public static function usePaginator(string|Paginator $paginator)
    {
        static::$defaultPaginator = $paginator;
    }

    /**
     * Configure the query parameter to use for the page number.
     *
     * @return void
     */
    public static function usePageKey(string $name)
    {
        static::$pageKey = $name;
    }

    /**
     * Get the records of the table.
     *
     * @return \Illuminate\Support\Collection<int,array<string,mixed>>|null
     */
    public function getRecords(): ?Collection
    {
        return $this->records;
    }

    /**
     * Determine if the table has records.
     */
    public function hasRecords(): bool
    {
        return ! \is_null($this->records);
    }

    /**
     * Set the records of the table.
     * 
     * @param  \Illuminate\Support\Collection<int,array<string,mixed>>  $records
     */
    public function setRecords(Collection $records): void
    {
        $this->records = $records;
    } 

    /**
     * Get the options for the number of items to show per page.
     *
     * @return int|array<int,int>
     */
    public function getPerPage(): int|array
    {
        return match (true) {
            \property_exists($this, 'perPage') => $this->perPage,
            \method_exists($this, 'perPage') => $this->perPage(),
            default => static::$defaultPerPageAmount
        };
    }

    /**
     * Get the default paginator to use.
     *
     * @return class-string|null
     */
    public function getPaginator(): string|null
    {
        return match (true) {
            \property_exists($this, 'paginator') => $this->paginator,
            \method_exists($this, 'paginator') => $this->paginator(),
            default => static::$defaultPaginator
        };
    }

    /**
     * Get the query parameter to use for the page number.
     */
    public function getPageKey(): string
    {
        return match (true) {
            \property_exists($this, 'page') => $this->page,
            default => static::$pageKey
        };
    }

    /**
     * Get the query parameter to use for the number of items to show.
     */
    public function getCountKey(): string
    {
        return match (true) {
            \property_exists($this, 'count') => $this->count,
            \method_exists($this, 'count') => $this->count(),
            default => static::$countKey
        };
    }

    /**
     * Get the pagination options for the number of items to show per page.
     *
     * @return array<int,array{value:int,active:bool}>
     */
    public function getPaginationCounts(int|null $active = null): array
    {
        $perPage = $this->getRecordsPerPage();

        return is_array($perPage)
            ? array_map(fn ($count) => ['value' => $count, 'active' => $count === $active], $perPage)
            : [['value' => $perPage, 'active' => true]];
    }

    public function getRecordsPerPage(): int|false
    {
        $request = request();

        if (\is_null($this->getPaginator())) {
            return false;
        }

        // Only an array can have pagination options, so short circuit if not an array
        if (! \is_array($this->getPerPage())) {
            return $this->getPerPage();
        }

        // Force integer
        $fromRequest = $request->integer($this->getPerPageName());

        // Loop over the options to create a serializable array

        // Must ensure the query param is in the array to prevent abuse of 1000s of records

        // 0 indicates no term is provided, so use the first option
        if ($fromRequest === 0) {
            return $this->getPerPage()[0];
        }

        return $this->getPerPage();
    }

    /**
     * Execute the query and paginate the results.
     */
    public function paginateRecords(Builder $query): Paginator|CursorPaginator|Collection
    {
        $paginator = match ($this->getPaginator()) {
            LengthAwarePaginator::class => $query->paginate(
                perPage: $this->getRecordsPerPage(),
                pageName: $this->getPageKey(),
            ),
            Paginator::class => $query->simplePaginate(
                perPage: $this->getRecordsPerPage(),
                pageName: $this->getPageKey(),
            ),
            CursorPaginator::class => $query->cursorPaginate(
                perPage: $this->getRecordsPerPage(),
                cursorName: $this->getPageKey(),
            ),
            null => $query->get(),
            default => throw new InvalidPaginatorException($this->getPaginator()),
        };

        return $paginator->withQueryString();
    }
}
