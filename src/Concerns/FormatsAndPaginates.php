<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait FormatsAndPaginates
{
    /**
     * @var int
     */
    protected $defaultPerPage;

    /**
     * @var int
     */
    protected static $useDefaultPerPage = 10;

    /**
     * @var int|array<int,int>
     */
    protected $perPage;

    /**
     * @var int|array<int,int>
     */
    protected static $usePerPage = 10;

    /**
     * @var string
     */
    protected $paginatorType;

    /**
     * @var string
     */
    protected static $usePaginatorType = LengthAwarePaginator::class;

    /**
     * @var string
     */
    protected $pageName;

    /**
     * @var string
     */
    protected static $usePageName = 'page';

    /**
     * @var string
     */
    protected $countName;

    /**
     * @var string
     */
    protected static $useCountName = 'show';

    /**
     * @var \Illuminate\Support\Collection<array-key, array<array-key, mixed>>|null
     */
    protected $records = null;

    /**
     * Configure the default number of items to show per page.
     * 
     * @param int $defaultPerPage
     * @return void
     */
    public static function useDefaultPerPage(int $defaultPerPage)
    {
        static::$useDefaultPerPage = $defaultPerPage;
    }

    /**
     * Configure the options for the number of items to show per page.
     * 
     * @param int|array<int,int> $perPage
     * @return void
     */
    public static function usePerPage(int|array $perPage)
    {
        static::$usePerPage = $perPage;
    }

    /**
     * Configure the default paginator to use.
     * 
     * @param string|\Honed\Table\Enums\Paginator $paginator
     * @return void
     */
    public static function usePaginator(string|Paginator $paginator)
    {
        static::$usePaginatorType = $paginator;
    }

    /**
     * Configure the query parameter to use for the page number.
     * 
     * @param string $pageName
     * @return void
     */
    public static function usePageName(string $pageName)
    {
        static::$usePageName = $pageName;
    }

    /**
     * Configure the query parameter to use for the number of items to show.
     * 
     * @param string $countName
     * @return void
     */
    public static function useCountName(string $countName)
    {
        static::$useCountName = $countName;
    }

    /**
     * Get the default number of items to show per page.
     * 
     * @return int
     */
    public function getDefaultRecordsPerPage()
    {
        return $this->inspect('defaultPerPage', static::$useDefaultPerPage);
    }

    /**
     * Get the options for the number of items to show per page.
     * 
     * @return int|array<int,int>
     */
    public function getRecordsPerPage()
    {
        return $this->inspect('perPage', static::$usePerPage);
    }

    /**
     * Get the default paginator to use.
     * 
     * @return string|\Honed\Table\Enums\Paginator
     */
    public function getPaginatorType()
    {
        return $this->inspect('paginatorType', static::$usePaginatorType);
    }

    /**
     * Get the query parameter to use for the page number.
     * 
     * @return string
     */
    public function getPageName()
    {
        return $this->inspect('pageName', static::$usePageName);
    }

    /**
     * Get the query parameter to use for the number of items to show.
     * 
     * @return string
     */
    public function getCountName()
    {
        return $this->inspect('countName', static::$useCountName);
    }

    /**
     * Get the pagination options for the number of items to show per page.
     * 
     * @param int|null $active
     * @return array<int, array{value: int, active: bool}>
     */
    public function getPaginationCounts(?int $active = null): array
    {
        $perPage = $this->getRecordsPerPage();

        return is_array($perPage)
            ? array_map(fn ($count) => ['value' => $count, 'active' => $count === $active], $perPage)
            : [['value' => $perPage, 'active' => true]];
    }

    /**
     * Get the number of items to show per page from the request query parameters.
     * 
     * @return int
     */
    public function getPageCount(): int
    {
        $count = $this->getRecordsPerPage();

        if (is_int($count)) {
            return $count;
        }
        if (in_array($term = $this->getCountAsTerm(), $count)) {
            return $term;
        }

        return $this->getDefaultRecordsPerPage();
    }

    /**
     * Execute the query and paginate the results.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function paginateRecords(Builder $query): Paginator|CursorPaginator|Collection
    {
        $paginator = match ($this->getPaginatorType()) {
            LengthAwarePaginator::class => $query->paginate(
                perPage: $this->getRecordsPerPage(),
                pageName: $this->getPageName(),
            ),
            Paginator::class => $query->simplePaginate(
                perPage: $this->getRecordsPerPage(),
                pageName: $this->getPageName(),
            ),
            CursorPaginator::class => $query->cursorPaginate(
                perPage: $this->getRecordsPerPage(),
                cursorName: $this->getPageName(),
            ),
            'none' => $query->get(),
            default => throw new \Exception("Invalid paginator type provided [{$this->getPaginatorType()}]"),
        };

        return $paginator->withQueryString();
    }
}
