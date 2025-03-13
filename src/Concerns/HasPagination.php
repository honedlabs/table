<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Core\Concerns\InterpretsRequest;
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
    protected $pagesKey;

    /**
     * The query parameter for the number of records to show per page.
     *
     * @var string|null
     */
    protected $recordsKey;

    /**
     * The records per page options if dynamic.
     *
     * @var array<int,\Honed\Table\PerPageRecord>
     */
    protected $recordsPerPage = [];

    /**
     * Retrieve the default paginator for the table.
     *
     * @return 'cursor'|'simple'|'length-aware'|'collection'|string
     */
    public function getPaginator()
    {
        return $this->paginator ?? static::fallbackPaginator();
    }

    /**
     * Retrieve the default paginator for the table.
     *
     * @return 'cursor'|'simple'|'length-aware'|'collection'|string
     */
    public static function fallbackPaginator()
    {
        return type(config('table.paginator', 'length-aware'))->asString();
    }

    /**
     * Retrieve the pagination options for the table.
     *
     * @return int|array<int,int>
     */
    public function getPagination()
    {
        if (isset($this->pagination)) {
            return $this->pagination;
        }

        if (\method_exists($this, 'pagination')) {
            return $this->pagination();
        }

        return static::fallbackPagination();
    }

    /**
     * Retrieve the pagination options for the table from the config.
     *
     * @return int|array<int,int>
     */
    public static function fallbackPagination()
    {
        /** @var int|array<int,int> */
        return config('table.pagination', 10);
    }

    /**
     * Retrieve the default pagination options for the table.
     *
     * @return int
     */
    public function getDefaultPagination()
    {
        return $this->defaultPagination ?? static::fallbackDefaultPagination();
    }

    /**
     * Get the fallback default pagination options for the table from the config.
     *
     * @return int
     */
    public static function fallbackDefaultPagination()
    {
        return type(config('table.default_pagination', 10))->asInt();
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
     * Set the query parameter for the page number.
     *
     * @param  string  $pagesKey
     * @return $this
     */
    public function pagesKey($pagesKey)
    {
        $this->pagesKey = $pagesKey;

        return $this;
    }

    /**
     * Get the query parameter for the page number.
     *
     * @return string
     */
    public function getPagesKey()
    {
        return $this->pagesKey ?? static::fallbackPagesKey();
    }

    /**
     * Get the query parameter for the page number from the config.
     *
     * @return string
     */
    public static function fallbackPagesKey()
    {
        return type(config('table.pages_key', 'page'))->asString();
    }

    /**
     * Set the query parameter for the number of records to show per page.
     *
     * @param  string  $recordsKey
     * @return $this
     */
    public function recordsKey($recordsKey)
    {
        $this->recordsKey = $recordsKey;

        return $this;
    }

    /**
     * Get the query parameter for the number of records to show per page.
     *
     * @return string
     */
    public function getRecordsKey()
    {
        return $this->recordsKey ?? static::fallbackRecordsKey();
    }

    /**
     * Get the query parameter for the number of records to show per page from
     * the config.
     *
     * @return string
     */
    public static function fallbackRecordsKey()
    {
        return type(config('table.records_key', 'rows'))->asString();
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
     * Determine if the paginator is a length-aware paginator.
     *
     * @param  string  $paginator
     * @return bool
     */
    protected static function isLengthAware($paginator)
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
    protected static function isSimple($paginator)
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
    protected static function isCursor($paginator)
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
    protected static function isCollector($paginator)
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
    protected static function lengthAwarePaginator($paginator)
    {
        return \array_merge(static::simplePaginator($paginator), [
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'firstLink' => $paginator->url(1),
            'lastLink' => $paginator->url($paginator->lastPage()),
            'links' => static::createPaginatorLinks($paginator),
        ]);
    }

    /**
     * Create pagination links with a sliding window around the current page.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator<TModel>  $paginator
     * @return array<int, array<string, mixed>>
     */
    protected static function createPaginatorLinks($paginator)
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $onEachSide = 2;

        // Calculate window boundaries with balanced distribution
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
    protected static function simplePaginator($paginator)
    {
        return \array_merge(static::cursorPaginator($paginator), [
            'currentPage' => $paginator->currentPage(),
        ]);
    }

    /**
     * Get the pagination data for the cursor paginator.
     *
     * @param  \Illuminate\Pagination\AbstractCursorPaginator<TModel>|\Illuminate\Contracts\Pagination\Paginator<TModel>  $paginator
     * @return array<string, mixed>
     */
    protected static function cursorPaginator($paginator)
    {
        return \array_merge(static::collectionPaginator($paginator), [
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
    protected static function collectionPaginator($paginator)
    {
        return [
            'empty' => $paginator->isEmpty(),
        ];
    }

    /**
     * Ensure that the pagination count is a valid option.
     *
     * @param  int|null  $count
     * @param  array<int,int>  $options
     * @return bool
     */
    protected function invalidPagination($count, $options)
    {
        return \is_null($count) || ! \in_array($count, $options);
    }

    /**
     * Get the number of records to show per page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    protected function getCount($request)
    {
        $pagination = $this->getPagination();

        if (! \is_array($pagination)) {
            return $pagination;
        }

        /** @var string */
        $param = $this->formatScope($this->getRecordsKey());

        $interpreter = new class
        {
            use InterpretsRequest;
        };

        $count = $interpreter->interpretInteger($request, $param);

        if ($this->invalidPagination($count, $pagination)) {
            $count = $this->getDefaultPagination();
        }

        $count = type($count)->asInt();

        $this->createRecordsPerPage($pagination, $count);

        return $count;
    }

    /**
     * Paginate the data.
     *
     * @param  TBuilder  $builder
     * @param  \Illuminate\Http\Request  $request
     * @return array{0: \Illuminate\Support\Collection<int,TModel>, 1: array<string,mixed>}
     *
     * @throws \InvalidArgumentException
     */
    protected function paginate($builder, $request)
    {
        $count = $this->getCount($request);

        $paginator = $this->getPaginator();
        $key = $this->getPagesKey();

        [$data, $method] = match (true) {
            static::isLengthAware($paginator) => [
                $builder->paginate($count, pageName: $key),
                'lengthAwarePaginator',
            ],
            static::isSimple($paginator) => [
                $builder->simplePaginate($count, pageName: $key),
                'simplePaginator',
            ],
            static::isCursor($paginator) => [
                $builder->cursorPaginate(perPage: $count, cursorName: $key),
                'cursorPaginator',
            ],
            static::isCollector($paginator) => [
                $builder->get(),
                'collectionPaginator',
            ],
            default => static::throwInvalidPaginatorException($paginator),
        };

        if (! $data instanceof Collection) {
            $data->withQueryString();
        }

        $paginationData = \call_user_func([static::class, $method], $data);

        return [
            $data instanceof Collection ? $data : collect($data->items()),
            $paginationData,
        ];
    }

    /**
     * Throw an exception if the paginator is invalid.
     *
     * @param  string  $paginator
     * @return never
     */
    protected static function throwInvalidPaginatorException($paginator)
    {
        throw new \InvalidArgumentException(
            \sprintf('The provided paginator [%s] is invalid.', $paginator)
        );
    }
}
