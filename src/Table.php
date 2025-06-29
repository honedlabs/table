<?php

declare(strict_types=1);

namespace Honed\Table;

use Closure;
use Honed\Action\Concerns\CanHandleOperations;
use Honed\Action\Contracts\HandlesOperations;
use Honed\Action\Handler;
use Honed\Action\Handlers\BatchHandler;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Contracts\HooksIntoLifecycle;
use Honed\Core\Contracts\NullsAsUndefined;
use Honed\Core\Contracts\Stateful;
use Honed\Core\Pipes\CallsAfter;
use Honed\Core\Pipes\CallsBefore;
use Honed\Core\Primitive;
use Honed\Infolist\Entries\Concerns\HasClasses;
use Honed\Refine\Concerns\CanRefine;
use Honed\Refine\Filters\Filter;
use Honed\Refine\Pipes\FilterQuery;
use Honed\Refine\Pipes\PersistData;
use Honed\Refine\Pipes\SearchQuery;
use Honed\Refine\Pipes\SortQuery;
use Honed\Refine\Searches\Search;
use Honed\Refine\Stores\CookieStore;
use Honed\Refine\Stores\SessionStore;
use Honed\Table\Columns\Column;
use Honed\Table\Concerns\HasColumns;
use Honed\Table\Concerns\HasEmptyState;
use Honed\Table\Concerns\HasRecords;
use Honed\Table\Concerns\Orderable;
use Honed\Table\Concerns\Paginable;
use Honed\Table\Concerns\Selectable;
use Honed\Table\Concerns\Toggleable;
use Honed\Table\Concerns\Viewable;
use Honed\Table\Exceptions\KeyNotFoundException;
use Honed\Table\Pipes\CreateEmptyState;
use Honed\Table\Pipes\Paginate;
use Honed\Table\Pipes\PrepareColumns;
use Honed\Table\Pipes\Query;
use Honed\Table\Pipes\Select;
use Honed\Table\Pipes\Toggle;
use Honed\Table\Pipes\TransformRecords;
use Illuminate\Container\Container;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Str;
use Throwable;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Primitive<string, mixed>
 *
 * @implements Stateful<string, mixed>
 */
class Table extends Primitive implements HandlesOperations, HooksIntoLifecycle, NullsAsUndefined, Stateful
{
    use CanHandleOperations;
    use CanRefine;
    use HasClasses;
    use HasColumns;
    use HasEmptyState;
    use HasMeta;
    use HasRecords;
    use Orderable;
    use Paginable;
    use Selectable;
    use Toggleable;
    use Viewable;

    /**
     * The default namespace where tables reside.
     *
     * @var string
     */
    public static $namespace = 'App\\Tables\\';

    /**
     * The identifier to use for evaluation.
     *
     * @var string
     */
    protected $evaluationIdentifier = 'table';

    /**
     * The unique identifier key for table records.
     *
     * @var string|null
     */
    protected $key;

    /**
     * The store to use for persisting the toggled columns.
     *
     * @var bool|string|null
     */
    protected $persistColumns = null;

    /**
     * How to resolve the table for the given model name.
     *
     * @var (Closure(class-string<\Illuminate\Database\Eloquent\Model>):class-string<Table>)|null
     */
    protected static $tableNameResolver;

    /**
     * Create a new table instance.
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $this->request($request);
    }

    /**
     * Create a new table instance.
     *
     * @param  Closure(TBuilder):void|null  $before
     * @return static
     */
    public static function make($before = null)
    {
        return resolve(static::class)
            ->when($before, fn ($table, $before) => $table->before($before));
    }

    /**
     * Get a new table instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @param  Closure|null  $before
     * @return Table<TClass>
     */
    public static function tableForModel($modelName, $before = null)
    {
        $table = static::resolveTableName($modelName);

        return $table::make($before);
    }

    /**
     * Get the table name for the given model name.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $className
     * @return class-string<Table>
     */
    public static function resolveTableName($className)
    {
        $resolver = static::$tableNameResolver ?? function (string $className) {
            $appNamespace = static::appNamespace();

            $className = Str::startsWith($className, $appNamespace.'Models\\')
                ? Str::after($className, $appNamespace.'Models\\')
                : Str::after($className, $appNamespace);

            /** @var class-string<Table> */
            return static::$namespace.$className.'Table';
        };

        return $resolver($className);
    }

    /**
     * Specify the default namespace that contains the application's model tables.
     *
     * @param  string  $namespace
     * @return void
     */
    public static function useNamespace($namespace)
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess the name of a model table.
     *
     * @param  Closure(class-string<\Illuminate\Database\Eloquent\Model>):class-string<Table>  $callback
     * @return void
     */
    public static function guessTableNamesUsing($callback)
    {
        static::$tableNameResolver = $callback;
    }

    /**
     * Flush the global configuration state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$encoder = null;
        static::$decoder = null;
        static::$tableNameResolver = null;
        static::$namespace = 'App\\Tables\\';
    }

    /**
     * Get the parent class for the instance.
     *
     * @return class-string<Table>
     */
    public static function getParentClass()
    {
        return self::class;
    }

    /**
     * Get the default endpoint to execute server actions.
     *
     * @return string
     */
    public static function getDefaultEndpoint()
    {
        /** @var string */
        return config('table.endpoint', 'table');
    }

    /**
     * Get the route key for the instance.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'table';
    }

    /**
     * Get the handler for the instance.
     *
     * @return class-string<\Honed\Action\Handlers\Handler<self>>
     */
    public function getHandler() // @phpstan-ignore-line
    {
        /** @var class-string<\Honed\Action\Handlers\Handler<self>> */
        return config('table.handler', BatchHandler::class);
    }

    /**
     * Set the record key to use.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the unique identifier key for table records.
     *
     * @return string
     *
     * @throws KeyNotFoundException
     */
    public function getKey()
    {
        if (isset($this->key)) {
            return $this->key;
        }

        $keyColumn = Arr::first(
            $this->getColumns(),
            static fn (Column $column): bool => $column->isKey()
        );

        if ($keyColumn) {
            return $keyColumn->getName();
        }

        KeyNotFoundException::throw(static::class);
    }

    /**
     * Set the store to use for persisting toggled columns.
     *
     * @param  bool|string|null  $store
     * @return $this
     */
    public function persistColumns($store = true)
    {
        $this->persistColumns = $store;

        return $this;
    }

    /**
     * Set the session store to be used for persisting toggled columns.
     *
     * @return $this
     */
    public function persistColumnsInSession()
    {
        return $this->persistColumns(SessionStore::NAME);
    }

    /**
     * Set the cookie store to be used for persisting toggled columns.
     *
     * @return $this
     */
    public function persistColumnsInCookie()
    {
        return $this->persistColumns(CookieStore::NAME);
    }

    /**
     * Determine if the toggled columns should be persisted.
     *
     * @return bool
     */
    public function shouldPersistColumns()
    {
        return (bool) $this->persistColumns;
    }

    /**
     * Get the store to use for persisting toggled columns.
     *
     * @return \Honed\Refine\Stores\Store|null
     */
    public function getColumnStore()
    {
        return $this->getStore($this->persistColumns);
    }

    /**
     * Determine if the table is empty using the pagination metadata.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return (bool) Arr::get($this->getPagination(), 'empty', true);
    }

    /**
     * Get the operations for the table as an array.
     *
     * @return array<string, mixed>
     */
    public function operationsToArray()
    {
        return [
            'inline' => filled($this->getInlineOperations()),
            'bulk' => $this->bulkOperationsToArray(),
            'page' => $this->pageOperationsToArray(),
        ];
    }

    /**
     * Get a snapshot of the current state of the instance.
     */
    public function toState(): array
    {
        Pipeline::send($this)
            ->through($this->refinements())
            ->thenReturn();

        return [
            ...$this->getSearchState(),
            ...$this->getSearchColumnsState(),
            ...$this->getSortState(),
            ...$this->getColumnsState(),
            ...$this->getFiltersState(),
        ];
    }

    /**
     * Get the search column state for the table.
     *
     * @return array<string, mixed>
     */
    public function getSearchColumnsState()
    {
        if ($this->isNotMatchable() || $this->isNotSearchable()) {
            return [];
        }

        $searches = array_map(
            static fn (Search $search) => $search->getParameter(),
            $this->getActiveSearches()
        );

        return [$this->getMatchKey() => implode($this->getDelimiter(), $searches)];
    }

    /**
     * Get the sort state for the table.
     *
     * @return array<string, mixed>
     */
    public function getSortState()
    {
        $sort = $this->getActiveSort();

        if ($sort) {
            return [$this->getSortKey() => $sort->getAscendingValue()];
        }

        return [];
    }

    /**
     * Get the filter state for the table.
     *
     * @return array<string, mixed>
     */
    public function getFiltersState()
    {
        if ($this->isNotFilterable()) {
            return [];
        }

        $filters = $this->getActiveFilters();

        return Arr::mapWithKeys(
            $filters,
            static fn (Filter $filter) => [$filter->getParameter() => $filter->getNormalizedValue()]
        );
    }

    /**
     * Get the column state for the table.
     *
     * @return array<string, mixed>
     */
    public function getColumnsState()
    {
        if ($this->isNotToggleable()) {
            return [];
        }

        $columns = array_map(
            static fn (Column $column) => $column->getParameter(),
            $this->getActiveColumns()
        );

        return [$this->getColumnKey() => implode($this->getDelimiter(), $columns)];
    }

    /**
     * Get the application namespace for the application.
     *
     * @return string
     */
    protected static function appNamespace()
    {
        try {
            return Container::getInstance()
                ->make(Application::class)
                ->getNamespace();
        } catch (Throwable) {
            return 'App\\';
        }
    }

    /**
     * Get the search term for the table.
     *
     * @return array<string, mixed>
     */
    protected function getSearchState()
    {
        if ($this->isNotSearchable()) {
            return [];
        }

        return [$this->getSearchKey() => $this->encodeSearchTerm($this->getSearchTerm())];
    }

    /**
     * Define the table.
     *
     * @param  $this  $table
     * @return $this
     */
    protected function definition(self $table): self
    {
        return $table;
    }

    /**
     * Get the representation of the instance.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        $this->build();

        return [
            ...$this->actionableToArray(),
            ...$this->refineToArray(),
            'key' => $this->getKey(),
            'column' => $this->isToggleable() ? $this->getColumnKey() : null,
            'record' => is_array($this->getPerPage()) ? $this->getRecordKey() : null,
            'page' => $this->getPageKey(),
            'records' => $this->getRecords(),
            'paginate' => $this->getPagination(),
            'columns' => $this->columnsToArray(),
            'pages' => $this->pageOptionsToArray(),
            'toggleable' => $this->isToggleable(),
            'operations' => $this->operationsToArray(),
            'emptyState' => $this->getEmptyState()?->toArray(),
            'views' => $this->listViews(),
            'meta' => $this->getMeta(),
        ];
    }

    /**
     * Get a partial set of pipes to be used for refining the resource, without
     * executing or persisting the data.
     *
     * @return array<int,class-string<\Honed\Core\Pipe>>
     */
    protected function refinements()
    {
        return [
            Toggle::class,
            CallsBefore::class,
            PrepareColumns::class,
            Select::class,
            SearchQuery::class,
            FilterQuery::class,
            SortQuery::class,
            Query::class,
            CallsAfter::class,
        ];
    }

    /**
     * Get the pipes to be used for building the table.
     *
     * @return array<int,class-string<\Honed\Core\Pipe>>
     */
    protected function pipes()
    {
        return [
            ...$this->refinements(),
            Paginate::class,
            TransformRecords::class,
            CreateEmptyState::class,
            PersistData::class,
        ];
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @param  string  $parameterName
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        return match ($parameterName) {
            'columns' => [$this->getColumns()],
            'headings' => [$this->getHeadings()],
            'emptyState' => [$this->newEmptyState()],
            'request' => [$this->getRequest()],
            'builder', 'query', 'q' => [$this->getBuilder()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * Provide a selection of default dependencies for evaluation by type.
     *
     * @param  string  $parameterType
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        $builder = $this->getBuilder();

        return match ($parameterType) {
            self::class => [$this],
            EmptyState::class => [$this->newEmptyState()],
            Request::class => [$this->getRequest()],
            $builder::class, Builder::class, BuilderContract::class => [$builder],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
