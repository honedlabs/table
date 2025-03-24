<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\PerPageRecord;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @template-covariant TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
trait HasPagination
{
    /**
     * The paginator to use.
     *
     * @var 'cursor'|'simple'|'length-aware'|'collection'|string|null
     */
    protected $paginator;

    /**
     * The pagination options.
     *
     * @var int|array<int,int>|null
     */
    protected $pagination;

    /**
     * The default pagination amount if pagination is an array.
     *
     * @var int|null
     */
    protected $defaultPagination;

    /**
     * The query parameter for the page number.
     *
     * @var string|null
     */
    protected $pageKey;

    /**
     * The query parameter for the number of records to show per page.
     *
     * @var string|null
     */
    protected $recordKey;

    /**
     * The number of page links to show either side of the current page.
     *
     * @var int|null
     */
    protected $window;

    /**
     * The records per page options if dynamic.
     *
     * @var array<int,\Honed\Table\PerPageRecord>
     */
    protected $recordsPerPage = [];

    /**
     * Set the paginator type.
     *
     * @param  'cursor'|'simple'|'length-aware'|'collection'|string  $paginator
     * @return $this
     */
    public function paginator($paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Get the paginator type.
     *
     * @return 'cursor'|'simple'|'length-aware'|'collection'|string
     */
    public function getPaginator()
    {
        if (isset($this->paginator)) {
            return $this->paginator;
        }

        return static::getDefaultPaginator();
    }

    /**
     * Get the default paginator type.
     *
     * @return 'cursor'|'simple'|'length-aware'|'collection'|string
     */
    public static function getDefaultPaginator()
    {
        return type(config('table.paginator', 'length-aware'))->asString();
    }

    /**
     * Set the pagination options.
     *
     * @param  int|array<int,int>  $pagination
     * @return $this
     */
    public function pagination($pagination)
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * Get the pagination options.
     *
     * @return int|array<int,int>
     */
    public function getPagination()
    {
        if (isset($this->pagination)) {
            return $this->pagination;
        }

        return static::getFallbackPagination();
    }

    /**
     * Get the pagination options from the config.
     *
     * @return int|array<int,int>
     */
    public static function getFallbackPagination()
    {
        /** @var int|array<int,int> */
        return config('table.pagination', 10);
    }

    /**
     * Set the default pagination amount.
     *
     * @param  int  $defaultPagination
     * @return $this
     */
    public function defaultPagination($defaultPagination)
    {
        $this->defaultPagination = $defaultPagination;

        return $this;
    }

    /**
     * Get the default pagination amount.
     *
     * @return int
     */
    public function getDefaultPagination()
    {
        if (isset($this->defaultPagination)) {
            return $this->defaultPagination;
        }

        return static::getDefaultedPagination();
    }

    /**
     * Get the fallback default pagination amount from the config.
     *
     * @return int
     */
    public static function getDefaultedPagination()
    {
        return type(config('table.default_pagination', 10))->asInt();
    }

    /**
     * Set the query parameter for the page number.
     *
     * @param  string  $pageKey
     * @return $this
     */
    public function pageKey($pageKey)
    {
        $this->pageKey = $pageKey;

        return $this;
    }

    /**
     * Get the query parameter for the page number.
     *
     * @return string
     */
    public function getPageKey()
    {
        if (isset($this->pageKey)) {
            return $this->pageKey;
        }

        return static::getDefaultPageKey();
    }

    /**
     * Get the query parameter for the page number from the config.
     *
     * @return string
     */
    public static function getDefaultPageKey()
    {
        return type(config('table.page_key', 'page'))->asString();
    }

    /**
     * Set the query parameter for the number of records to show per page.
     *
     * @param  string  $recordKey
     * @return $this
     */
    public function recordKey($recordKey)
    {
        $this->recordKey = $recordKey;

        return $this;
    }

    /**
     * Get the query parameter for the number of records to show per page.
     *
     * @return string
     */
    public function getRecordKey()
    {
        if (isset($this->recordKey)) {
            return $this->recordKey;
        }

        return static::getDefaultRecordKey();
    }

    /**
     * Get the default query parameter for the number of records to show per
     * page.
     *
     * @return string
     */
    public static function getDefaultRecordKey()
    {
        return type(config('table.record_key', 'rows'))->asString();
    }

    /**
     * Set the number of page links to show either side of the current page.
     *
     * @param  int  $window
     * @return $this
     */
    public function window($window)
    {
        $this->window = $window;

        return $this;
    }

    /**
     * Get the number of page links to show either side of the current page.
     *
     * @return int
     */
    public function getWindow()
    {
        if (isset($this->window)) {
            return $this->window;
        }

        return static::getDefaultWindow();
    }

    /**
     * Get the default number of page links to show either side of the current
     * page.
     *
     * @return int
     */
    public static function getDefaultWindow()
    {
        return type(config('table.window', 2))->asInt();
    }

    /**
     * Create the record per page options for the table.
     *
     * @param  array<int,int>  $pagination
     * @param  int  $active
     * @return void
     */
    public function createRecordsPerPage($pagination, $active)
    {
        $this->recordsPerPage = \array_map(
            static fn (int $amount) => PerPageRecord::make($amount, $active),
            $pagination
        );
    }

    /**
     * Get records per page options.
     *
     * @return array<int,\Honed\Table\PerPageRecord>
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     * Get the records per page options as an array.
     *
     * @return array<int,array<string,mixed>>
     */
    public function recordsPerPageToArray()
    {
        return \array_map(
            static fn (PerPageRecord $record) => $record->toArray(),
            $this->getRecordsPerPage()
        );
    }

    /**
     * Determine if the paginator is a length-aware paginator.
     *
     * @param  string  $paginator
     * @return bool
     */
    public function isLengthAware($paginator)
    {
        return \in_array($paginator, [
            'length-aware',
            \Illuminate\Contracts\Pagination\LengthAwarePaginator::class,
            \Illuminate\Pagination\LengthAwarePaginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a simple paginator.
     *
     * @param  string  $paginator
     * @return bool
     */
    public function isSimple($paginator)
    {
        return \in_array($paginator, [
            'simple',
            \Illuminate\Contracts\Pagination\Paginator::class,
            \Illuminate\Pagination\Paginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a cursor paginator.
     *
     * @param  string  $paginator
     * @return bool
     */
    public function isCursor($paginator)
    {
        return \in_array($paginator, [
            'cursor',
            \Illuminate\Contracts\Pagination\CursorPaginator::class,
            \Illuminate\Pagination\CursorPaginator::class,
        ]);
    }

    /**
     * Determine if the paginator is a collection.
     *
     * @param  string  $paginator
     * @return bool
     */
    public function isCollector($paginator)
    {
        return \in_array($paginator, [
            'none',
            'collection',
            \Illuminate\Support\Collection::class,
        ]);
    }

    /**
     * Get the pagination data for the length-aware paginator.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator<TModel>  $paginator
     * @return array<string, mixed>
     */
    public function lengthAwarePaginator($paginator)
    {
        return \array_merge($this->simplePaginator($paginator), [
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'firstLink' => $paginator->url(1),
            'lastLink' => $paginator->url($paginator->lastPage()),
            'links' => $this->createPaginatorLinks($paginator),
        ]);
    }

    /**
     * Create pagination links with a sliding window around the current page.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator<TModel>  $paginator
     * @return array<int, array<string, mixed>>
     */
    public function createPaginatorLinks($paginator)
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $onEachSide = $this->getWindow();

        $start = max(1, min($currentPage - $onEachSide, $lastPage - ($onEachSide * 2)));
        $end = min($lastPage, max($currentPage + $onEachSide, ($onEachSide * 2 + 1)));

        return \array_map(
            static fn (int $page) => [
                'url' => $paginator->url($page),
                'label' => (string) $page,
                'active' => $currentPage === $page,
            ],
            range($start, $end)
        );
    }

    /**
     * Get the pagination data for the simple paginator.
     *
     * @param  \Illuminate\Contracts\Pagination\Paginator<TModel>  $paginator
     * @return array<string, mixed>
     */
    public function simplePaginator($paginator)
    {
        return \array_merge($this->cursorPaginator($paginator), [
            'currentPage' => $paginator->currentPage(),
        ]);
    }

    /**
     * Get the pagination data for the cursor paginator.
     *
     * @param  \Illuminate\Pagination\AbstractCursorPaginator<TModel>|\Illuminate\Contracts\Pagination\Paginator<TModel>  $paginator
     * @return array<string, mixed>
     */
    public function cursorPaginator($paginator)
    {
        return \array_merge($this->collectionPaginator($paginator), [
            'prevLink' => $paginator->previousPageUrl(),
            'nextLink' => $paginator->nextPageUrl(),
            'perPage' => $paginator->perPage(),
        ]);
    }

    /**
     * Get the base metadata for the collection paginator, and all others.
     *
     * @param  \Illuminate\Support\Collection<int,TModel>|\Illuminate\Pagination\AbstractCursorPaginator<TModel>|\Illuminate\Contracts\Pagination\Paginator<TModel>  $paginator
     * @return array<string, mixed>
     */
    public function collectionPaginator($paginator)
    {
        return [
            'empty' => $paginator->isEmpty(),
        ];
    }
}
