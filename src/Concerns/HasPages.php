<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Exceptions\InvalidPaginatorException;
use Honed\Table\PageAmount;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator as PaginationCursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Support\Collection;

trait HasPages
{
    /**
     * @var \Illuminate\Support\Collection<int,\Honed\Table\PageAmount>|null
     */
    protected $pages;

    /**
     * @var int|array<int,int>
     */
    protected $perPage;

    /**
     * @var int|array<int,int>
     */
    protected static $globalPerPage = 10;

    /**
     * @var int
     */
    protected $defaultPerPage;

    /**
     * @var int
     */
    protected static $defaultPerPageAmount = 10;

    /**
     * @var 'cursor'|'simple'|'length-aware'|class-string<\Illuminate\Contracts\Pagination\Paginator>|null
     */
    protected $paginator;

    /**
     * @var 'cursor'|'simple'|'length-aware'|class-string<\Illuminate\Contracts\Pagination\Paginator>
     */
    protected static $defaultPaginator = LengthAwarePaginator::class;

    /**
     * @var string
     */
    protected $page;

    /**
     * Use the default page key.
     *
     * @var string|null
     */
    protected static $pageKey = null;

    /**
     * @var string
     */
    protected $shown;

    /**
     * @var string
     */
    protected static $shownKey = 'show';

    /**
     * Configure the options for the default number of records to show per page.
     *
     * @param  int|non-empty-array<int,int>  $perPage
     */
    public static function recordsPerPage(int|array $perPage = 10): void
    {
        static::$globalPerPage = $perPage;
    }

    /**
     * Configure the paginator to use.
     *
     * @param  'cursor'|'simple'|'length-aware'|class-string<\Illuminate\Contracts\Pagination\Paginator>|null  $paginator
     */
    public static function usePaginator(?string $paginator = null): void
    {
        static::$defaultPaginator = $paginator ?? LengthAwarePaginator::class;
    }

    /**
     * Configure the query parameter name to use for the current page number being shown.
     */
    public static function usePageKey(?string $name = null): void
    {
        static::$pageKey = $name;
    }

    /**
     * Configure the query parameter name to use for the number of records to display.
     */
    public static function useShownKey(?string $shown = null): void
    {
        static::$shownKey = $shown ?? 'show';
    }

    /**
     * Get the options for the number of records to show per page.
     *
     * @return int|non-empty-array<int,int>
     */
    public function getPerPage(): int|array
    {
        return match (true) {
            \property_exists($this, 'perPage') && ! \is_null($this->perPage) => $this->perPage,
            \method_exists($this, 'perPage') => $this->perPage(),
            default => static::$globalPerPage
        };
    }

    /**
     * Get default per page amount.
     */
    public function getDefaultPerPage(): int
    {
        return match (true) {
            \property_exists($this, 'defaultPerPage') && ! \is_null($this->defaultPerPage) => $this->defaultPerPage,
            \method_exists($this, 'defaultPerPage') => $this->defaultPerPage(),
            default => static::$defaultPerPageAmount
        };
    }

    /**
     * Get the paginator to use.
     *
     * @return 'cursor'|'simple'|'length-aware'|class-string<\Illuminate\Contracts\Pagination\Paginator>|null
     */
    public function getPaginator(): ?string
    {
        return match (true) {
            \property_exists($this, 'paginator') && ! \is_null($this->paginator) => $this->paginator,
            \method_exists($this, 'paginator') => $this->paginator(),
            default => static::$defaultPaginator
        };
    }

    /**
     * Get the query parameter to use for the current page number being shown.
     */
    public function getPageKey(): ?string
    {
        return match (true) {
            \property_exists($this, 'page') && ! \is_null($this->page) => $this->page,
            \method_exists($this, 'page') => $this->page(),
            default => static::$pageKey
        };
    }

    /**
     * Get the query parameter to use for the number of records to show per page.
     */
    public function getShownKey(): string
    {
        return match (true) {
            \property_exists($this, 'shown') && ! \is_null($this->shown) => $this->shown,
            \method_exists($this, 'shown') => $this->shown(),
            default => static::$shownKey
        };
    }

    /**
     * Set the paginator to use.
     *
     * @param  'cursor'|'simple'|'length-aware'|class-string<\Illuminate\Contracts\Pagination\Paginator>|null  $paginator
     */
    public function setPaginator(?string $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * Set the page amount options quietly.
     *
     * @param  \Illuminate\Support\Collection<int,\Honed\Table\PageAmount>  $pages
     */
    public function setPages(Collection $pages): void
    {
        $this->pages = $pages;
    }

    /**
     * Get the page amount options.
     *
     * @return \Illuminate\Support\Collection<int,\Honed\Table\PageAmount>|null
     */
    public function getPages(): ?Collection
    {
        return $this->pages;
    }

    /**
     * Determine if the page amount options have been set.
     */
    public function hasPages(): bool
    {
        return ! \is_null($this->pages);
    }

    /**
     * Retrieve the records to use for pagination for the given request, setting the page options if applicable.
     */
    public function getRecordsPerPage(?Request $request = null): int
    {
        $perPageOptions = $this->getPerPage();

        if (! \is_array($perPageOptions)) {
            return $perPageOptions;
        }

        $requestedAmount = ($request ?? request())
            ->integer($this->getShownKey(), null);

        // dd($requestedAmount);

        $currentAmount = in_array($requestedAmount, $perPageOptions, true)
            ? $requestedAmount
            : $this->getDefaultPerPage();

        $this->setPages(collect($perPageOptions)
            ->map(static fn (int $option) => PageAmount::make($option, $option === $currentAmount)));

        return $currentAmount;
    }

    /**
     * Execute the query and paginate the results.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Support\Collection
     *
     * @throws \Honed\Table\Exceptions\InvalidPaginatorException
     */
    public function paginateRecords(Builder $query, ?Request $request = null): mixed
    {
        $paginator = $this->getPaginator();

        $paginated = match (true) {
            \in_array($paginator, ['length-aware',
                LengthAwarePaginator::class,
                PaginationLengthAwarePaginator::class,
            ]) => $query->paginate(
                perPage: $this->getRecordsPerPage($request),
                pageName: $this->getPageKey(),
            ),
            \in_array($paginator, ['simple', Paginator::class]) => $query->simplePaginate(
                perPage: $this->getRecordsPerPage($request),
                pageName: $this->getPageKey(),
            ),
            \in_array($paginator, ['cursor', CursorPaginator::class, PaginationCursorPaginator::class]) => $query->cursorPaginate(
                perPage: $this->getRecordsPerPage($request),
                cursorName: $this->getPageKey(),
            ),
            \in_array($paginator, [null,
                'none',
                'collection',
                Collection::class,
            ]) => $query->get(),
            default => throw new InvalidPaginatorException($paginator),
        };

        return $paginated instanceof Collection ? $paginated : $paginated->withQueryString();
    }
}
