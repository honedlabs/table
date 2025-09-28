<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\PageOption;
use Illuminate\Database\Eloquent\Builder;

use function array_map;

trait Paginable
{
    public const CURSOR = 'cursor';

    public const SIMPLE = 'simple';

    public const LENGTH_AWARE = 'length-aware';

    public const COLLECTION = 'collection';

    public const PAGE_KEY = 'page';

    public const RECORD_KEY = 'rows';

    public const PER_PAGE = 10;

    public const WINDOW = 2;

    /**
     * The callback to use for custom pagination.
     *
     * @var callable(Builder,int,string,int):Builder
     */
    protected $paginateUsing;

    /**
     * The paginator to use.
     *
     * @var string
     */
    protected $paginate = self::LENGTH_AWARE;

    /**
     * The pagination options.
     *
     * @var int|array<int,int>
     */
    protected $perPage = self::PER_PAGE;

    /**
     * The default pagination amount if pagination is an array.
     *
     * @var int
     */
    protected $defaultPerPage = self::PER_PAGE;

    /**
     * The query parameter for the page number.
     *
     * @var string
     */
    protected $pageKey = self::PAGE_KEY;

    /**
     * The query parameter for the number of records to show per page.
     *
     * @var string
     */
    protected $recordKey = self::RECORD_KEY;

    /**
     * The number of page links to show either side of the current page.
     *
     * @var int
     */
    protected $window = self::WINDOW;

    /**
     * The records per page options if dynamic.
     *
     * @var array<int,PageOption>
     */
    protected $pageOptions = [];

    /**
     * Register the callback to use for custom pagination.
     *
     * @param  callable(Builder,int,string,int):Builder  $callback
     * @return $this
     */
    public function paginateUsing(callable $callback): static
    {
        $this->paginateUsing = $callback;

        return $this;
    }

    /**
     * Call the paginator callback.
     *
     * @return mixed
     */
    public function callPaginator(Builder $builder, $value)
    {
        if (isset($this->paginateUsing)) {
            return ($this->paginateUsing)($builder, $value, $this->getPageKey(), $this->getWindow());
        }

        return null;
    }

    /**
     * Set the paginator type.
     *
     * @param  bool|string  $value
     * @return $this
     */
    public function paginate($value = true)
    {
        $this->paginate = match ($value) {
            true => self::LENGTH_AWARE,
            false => self::COLLECTION,
            default => $value,
        };

        return $this;
    }

    /**
     * Set the instance to not be paginable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function dontPaginate($value = true)
    {
        return $this->paginate(! $value);
    }

    /**
     * Set the paginator type to be 'length-aware'.
     *
     * @return $this
     */
    public function lengthAwarePaginate()
    {
        return $this->paginate(self::LENGTH_AWARE);
    }

    /**
     * Set the paginator type to be 'simple'.
     *
     * @return $this
     */
    public function simplePaginate()
    {
        return $this->paginate(self::SIMPLE);
    }

    /**
     * Set the paginator type to be 'cursor'.
     *
     * @return $this
     */
    public function cursorPaginate()
    {
        return $this->paginate(self::CURSOR);
    }

    /**
     * Get the paginator type.
     *
     * @return string
     */
    public function getPaginate()
    {
        return $this->paginate;
    }

    /**
     * Set the pagination options.
     *
     * @param  int|array<int,int>  $perPage
     * @return $this
     */
    public function perPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the pagination options.
     *
     * @return int|array<int,int>
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set the default pagination amount.
     *
     * @param  int  $perPage
     * @return $this
     */
    public function defaultPerPage($perPage)
    {
        $this->defaultPerPage = $perPage;

        return $this;
    }

    /**
     * Get the default pagination amount.
     *
     * @return int
     */
    public function getDefaultPerPage()
    {
        return $this->defaultPerPage;
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
        return $this->scoped($this->pageKey);
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
        return $this->scoped($this->recordKey);
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
        return $this->window;
    }

    /**
     * Create the record per page options for the table.
     *
     * @param  array<int,int>  $pagination
     * @param  int  $active
     * @return void
     */
    public function createPageOptions($pagination, $active)
    {
        $this->pageOptions = array_map(
            static fn (int $amount) => PageOption::make($amount, $active),
            $pagination
        );
    }

    /**
     * Get records per page options.
     *
     * @return array<int,PageOption>
     */
    public function getPageOptions()
    {
        return $this->pageOptions;
    }

    /**
     * Get the records per page options as an array.
     *
     * @return array<int,array<string,mixed>>
     */
    public function pageOptionsToArray()
    {
        return array_map(
            static fn (PageOption $record) => $record->toArray(),
            $this->getPageOptions()
        );
    }
}
