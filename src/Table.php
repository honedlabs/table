<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Contracts\Handles;
use Honed\Action\Handler;
use Honed\Core\Concerns\HasMeta;
use Honed\Refine\Pipelines\AfterRefining;
use Honed\Refine\Pipelines\BeforeRefining;
use Honed\Refine\Refine;
use Honed\Table\Columns\Column;
use Honed\Table\Concerns\HasColumns;
use Honed\Table\Concerns\HasPagination;
use Honed\Table\Concerns\IsToggleable;
use Honed\Table\Contracts\ShouldSelect;
use Honed\Table\Exceptions\KeyNotFoundException;
use Honed\Table\Pipelines\CleanupTable;
use Honed\Table\Pipelines\CreateEmptyState;
use Honed\Table\Pipelines\Paginate;
use Honed\Table\Pipelines\QueryColumns;
use Honed\Table\Pipelines\RefineFilters;
use Honed\Table\Pipelines\RefineSearches;
use Honed\Table\Pipelines\RefineSorts;
use Honed\Table\Pipelines\SelectColumns;
use Honed\Table\Pipelines\ToggleColumns;
use Honed\Table\Pipelines\TransformRecords;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Refine<TModel, TBuilder>
 */
class Table extends Refine implements Handles, UrlRoutable
{
    /** @use HasActions<TModel, TBuilder> */
    use HasActions;

    /** @use HasColumns<TModel, TBuilder> */
    use HasColumns;

    use HasEncoder;
    use HasEndpoint;
    use HasMeta;

    /** @use HasPagination<TModel, TBuilder> */
    use HasPagination {
        getPageKey as protected getBasePageKey;
        getRecordKey as protected getBaseRecordKey;
    }

    /** @use IsToggleable<TModel, TBuilder> */
    use IsToggleable {
        getColumnKey as protected getBaseColumnKey;
    }

    /**
     * The unique identifier column for the table.
     *
     * @var string|null
     */
    protected $key;

    /**
     * The table records.
     *
     * @var array<int,mixed>
     */
    protected $records = [];

    /**
     * The pagination data of the table.
     *
     * @var array<string,mixed>
     */
    protected $paginationData = [];

    /**
     * Whether the model should be serialized per record.
     *
     * @var bool|null
     */
    protected $serialize;

    /**
     * The empty state of the table.
     *
     * @var \Honed\Table\EmptyState|null
     */
    protected $emptyState;

    /**
     * Whether to do column selection.
     *
     * @var bool|null
     */
    protected $select;

    /**
     * The columns to always be selected.
     *
     * @var array<int,string>
     */
    protected $selects = [];

    /**
     * The default namespace where tables reside.
     *
     * @var string
     */
    public static $namespace = 'App\\Tables\\';

    /**
     * How to resolve the table for the given model name.
     *
     * @var (\Closure(class-string):class-string<\Honed\Table\Table>)|null
     */
    protected static $tableNameResolver;

    /**
     * Create a new table instance.
     *
     * @param  \Closure(TBuilder):void|null  $before
     * @return static
     */
    public static function make($before = null)
    {
        return resolve(static::class)
            ->before($before);
    }

    /**
     * Build the table. Alias for `refine`.
     *
     * @return $this
     */
    public function build()
    {
        return $this->refine();
    }

    /**
     * {@inheritdoc}
     */
    public static function baseClass()
    {
        return Table::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKeyName()
    {
        return 'table';
    }

    /**
     * {@inheritdoc}
     */
    public function handle($request)
    {
        if ($this->isntActionable()) {
            abort(404);
        }

        try {
            return Handler::make(
                $this->getResource(),
                $this->getActions(),
                $this->getKey()
            )->handle($request);
        } catch (\RuntimeException $e) {
            abort(404);
        }
    }

    /**
     * Set the record key to use.
     *
     * @param  string  $key
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
     * @throws \Honed\Table\Exceptions\KeyNotFoundException
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
     * Set whether the model attributes should serialized alongside columns.
     *
     * @param  bool|null  $serialize
     * @return $this
     */
    public function serializes($serialize = true)
    {
        $this->serialize = $serialize;

        return $this;
    }

    /**
     * Get whether the model should be serialized per record.
     *
     * @return bool
     */
    public function isSerialized()
    {
        if (isset($this->serialize)) {
            return $this->serialize;
        }

        return static::isSerializedByDefault();
    }

    /**
     * Get whether the model should be serialized per record from the config.
     *
     * @return bool
     */
    public static function isSerializedByDefault()
    {
        return (bool) config('table.serialize', false);
    }

    /**
     * Set the records for the table.
     *
     * @param  array<int,mixed>  $records
     * @return void
     */
    public function setRecords($records)
    {
        $this->records = $records;
    }

    /**
     * Get the records from the table.
     *
     * @return array<int,mixed>
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * Set the pagination data for the table.
     *
     * @param  array<string,mixed>  $paginationData
     * @return void
     */
    public function setPaginationData($paginationData)
    {
        $this->paginationData = $paginationData;
    }

    /**
     * Get the pagination data from the table.
     *
     * @return array<string,mixed>
     */
    public function getPaginationData()
    {
        return $this->paginationData;
    }

    /**
     * Set the empty state of the table.
     *
     * @param  \Honed\Table\EmptyState|string|\Closure(\Honed\Table\EmptyState):mixed  $message
     * @param  string|null  $title
     * @return $this
     */
    public function emptyState($message, $title = null)
    {
        $emptyState = $this->getEmptyState();

        if (\is_string($message)) {
            $emptyState->message($message)->title($title);
        } elseif ($message instanceof \Closure) {
            $message($emptyState);
        } else {
            $this->emptyState = $message;
        }

        return $this;
    }

    /**
     * Get the empty state of the table.
     *
     * @return \Honed\Table\EmptyState
     */
    public function getEmptyState()
    {
        return $this->emptyState ??= EmptyState::make();
    }

    /**
     * Define the empty state of the table.
     *
     * @param  \Honed\Table\EmptyState  $emptyState
     * @return void
     */
    public function defineEmptyState($emptyState)
    {
        //
    }

    /**
     * Set whether to do column selection.
     *
     * @param  bool  $select
     * @return $this
     */
    public function select($select = true)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * Determine whether to do column selection.
     *
     * @return bool
     */
    public function isSelectable()
    {
        if (isset($this->select)) {
            return $this->select;
        }

        if ($this instanceof ShouldSelect) {
            return true;
        }

        return static::isSelectableByDefault();
    }

    /**
     * Whether to do column selection by default.
     *
     * @return bool
     */
    public function isSelectableByDefault()
    {
        return (bool) config('table.select', false);
    }

    /**
     * Set the columns to always have selected.
     *
     * @param  string|iterable<int,string>  $selects
     * @return $this
     */
    public function selects(...$selects)
    {
        $this->select();

        $selects = Arr::flatten($selects);

        $this->selects = \array_merge($this->selects, $selects);

        return $this;
    }

    /**
     * Get the columns to always have selected.
     *
     * @return array<int,string>
     */
    public function getSelects()
    {
        return $this->selects;
    }

    /**
     * Get the query parameter for the page number.
     *
     * @return string
     */
    public function getPageKey()
    {
        return $this->formatScope($this->getBasePageKey());
    }

    /**
     * Get the query parameter for the number of records to show per page.
     *
     * @return string
     */
    public function getRecordKey()
    {
        return $this->formatScope($this->getBaseRecordKey());
    }

    /**
     * Get the query parameter for which columns to display.
     *
     * @return string
     */
    public function getColumnKey()
    {
        return $this->formatScope($this->getBaseColumnKey());
    }

    /**
     * Get the endpoint to be used for table actions from the config.
     *
     * @return string
     */
    public static function getDefaultEndpoint()
    {
        return type(config('table.endpoint', '/table'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultDelimiter()
    {
        return type(config('table.delimiter', ','))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultSearchKey()
    {
        return type(config('table.search_key', 'search'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultMatchKey()
    {
        return type(config('table.match_key', 'match'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public static function isMatchingByDefault()
    {
        return (bool) config('table.match', false);
    }

    /**
     * Get a new table instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @param  \Closure|null  $before
     * @return \Honed\Table\Table<TClass>
     */
    public static function tableForModel($modelName, $before = null)
    {
        $table = static::resolveTableName($modelName);

        return $table::make($before);
    }

    /**
     * Get the table name for the given model name.
     *
     * @param  class-string  $className
     * @return class-string<\Honed\Table\Table>
     */
    public static function resolveTableName($className)
    {
        $resolver = static::$tableNameResolver ?? function (string $className) {
            $appNamespace = static::appNamespace();

            $className = Str::startsWith($className, $appNamespace.'Models\\')
                ? Str::after($className, $appNamespace.'Models\\')
                : Str::after($className, $appNamespace);

            /** @var class-string<\Honed\Table\Table> */
            return static::$namespace.$className.'Table';
        };

        return $resolver($className);
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
        } catch (\Throwable) {
            return 'App\\';
        }
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
     * @param  \Closure(class-string):class-string<\Honed\Table\Table>  $callback
     * @return void
     */
    public static function guessTableNamesUsing($callback)
    {
        static::$tableNameResolver = $callback;
    }

    /**
     * Flush the table's global configuration state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$tableNameResolver = null;
        static::$namespace = 'App\\Tables\\';
    }

    /**
     * {@inheritdoc}
     */
    public function configToArray()
    {
        $config = \array_merge(parent::configToArray(), [
            'key' => $this->getKey(),
            'record' => $this->getRecordKey(),
            'column' => $this->getColumnKey(),
            'page' => $this->getPageKey(),
        ]);

        if ($this->isExecutable(static::baseClass())) {
            return \array_merge($config, [
                'endpoint' => $this->getEndpoint(),
            ]);
        }

        return $config;
    }

    /**
     * Get the actions for the table as an array.
     *
     * @return array<string, mixed>
     */
    public function actionsToArray()
    {
        return [
            'inline' => filled($this->getInlineActions()),
            'bulk' => $this->getBulkActions(),
            'page' => $this->getPageActions(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $this->build();

        $table = \array_merge(parent::toArray(), [
            'records' => $this->getRecords(),
            'paginator' => $this->getPaginationData(),
            'columns' => $this->columnsToArray(),
            'perPage' => $this->recordsPerPageToArray(),
            'toggles' => $this->isToggleable(),
            'actions' => $this->actionsToArray(),
            'meta' => $this->getMeta(),
        ]);

        if (Arr::get($this->getPaginationData(), 'empty', false)) {
            $table = \array_merge($table, [
                'empty' => $this->getEmptyState()->toArray(),
            ]);
        }

        if ($this->isExecutable(static::baseClass())) {
            return \array_merge($table, [
                'id' => $this->getRouteKey(),
            ]);
        }

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    protected function pipeline()
    {
        App::make(Pipeline::class)
            ->send($this)
            ->through([
                BeforeRefining::class,
                ToggleColumns::class,
                RefineSearches::class,
                RefineFilters::class,
                RefineSorts::class,
                SelectColumns::class,
                QueryColumns::class,
                AfterRefining::class,
                Paginate::class,
                TransformRecords::class,
                CreateEmptyState::class,
                CleanupTable::class,
            ])->thenReturn();
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        return $this->macroCall($method, $parameters);
    }
}
