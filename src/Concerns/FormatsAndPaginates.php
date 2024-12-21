<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait FormatsAndPaginates
{
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
    protected $page;

    /**
     * @var string
     */
    protected static $pageName = 'page';

    /**
     * @var string
     */
    protected $show;

    /**
     * @var string
     */
    protected static $showName = 'show';

    /**
     * @var \Illuminate\Support\Collection<array-key, array<array-key, mixed>>|null
     */
    protected $records = null;

    /**
     * Configure the options for the number of items to show per page.
     *
     * @param  int|array<int,int>  $perPage
     * @return void
     */
    public static function usePerPage(int|array $perPage)
    {
        static::$usePerPage = $perPage;
    }

    /**
     * Configure the default paginator to use.
     *
     * @param  string|\Honed\Table\Enums\Paginator  $paginator
     * @return void
     */
    public static function usePaginator(string|Paginator $paginator)
    {
        static::$usePaginatorType = $paginator;
    }

    /**
     * Configure the query parameter to use for the page number.
     *
     * @param  string  $name
     * @return void
     */
    public static function pageName(string $name)
    {
        static::$pageName = $name;
    }

    /**
     * Configure the query parameter to use for the number of items to show.
     *
     * @param  string  $name
     * @return void
     */
    public static function showName(string $name)
    {
        static::$showName = $name;
    }

    /**
     * Get the options for the number of items to show per page.
     *
     * @return int|array<int,int>
     */
    public function getPerPage()
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
        return $this->inspect('page', static::$pageName);
    }

    /**
     * Get the query parameter to use for the number of items to show.
     *
     * @return string
     */
    public function getShowName()
    {
        return $this->inspect('show', static::$showName);
    }

    /**
     * Get the pagination options for the number of items to show per page.
     *
     * @return array<int, array{value: int, active: bool}>
     */
    public function getPaginationCounts(?int $active = null): array
    {
        $perPage = $this->getRecordsPerPage();

        return is_array($perPage)
            ? array_map(fn ($count) => ['value' => $count, 'active' => $count === $active], $perPage)
            : [['value' => $perPage, 'active' => true]];
    }

    public function getRecordsPerPage(): int|false
    {
        $request = request();
    
        if ($this->getPaginatorType() === 'none') {
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
