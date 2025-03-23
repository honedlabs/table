<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Handler;
use Honed\Core\Concerns\Encodable;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasParameterNames;
use Honed\Refine\Pipelines\AfterRefining;
use Honed\Refine\Pipelines\BeforeRefining;
use Honed\Refine\Refine;
use Honed\Table\Columns\Column;
use Honed\Table\Concerns\HasColumns;
use Honed\Table\Concerns\HasPagination;
use Honed\Table\Concerns\HasTableBindings;
use Honed\Table\Concerns\IsSelectable;
use Honed\Table\Concerns\IsToggleable;
use Honed\Table\Pipelines\CleanupTable;
use Honed\Table\Pipelines\Paginate;
use Honed\Table\Pipelines\QueryColumns;
use Honed\Table\Pipelines\RefineFilters;
use Honed\Table\Pipelines\RefineSearches;
use Honed\Table\Pipelines\RefineSorts;
use Honed\Table\Pipelines\SelectColumns;
use Honed\Table\Pipelines\ToggleColumns;
use Honed\Table\Pipelines\TransformRecords;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Refine<TModel, TBuilder>
 */
class Table extends Refine implements UrlRoutable
{
    use Encodable;
    use HasActions;

    /** @use HasColumns<TModel, TBuilder> */
    use HasColumns;

    use HasMeta;

    /** @use HasPagination<TModel, TBuilder> */
    use HasPagination;

    /** @use HasParameterNames<TModel, TBuilder> */
    use HasParameterNames;

    use HasTableBindings;

    /** @use IsSelectable<TModel, TBuilder> */
    use IsSelectable;

    /** @use IsToggleable<TModel, TBuilder> */
    use IsToggleable;

    /**
     * The unique identifier column for the table.
     *
     * @var string|null
     */
    protected $key;

    /**
     * The endpoint to be used to handle table actions.
     *
     * @var string|null
     */
    protected $endpoint;

    /**
     * Whether the model should be serialized per record.
     *
     * @var bool|null
     */
    protected $attributes;

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
     * Create a new table instance.
     *
     * @param  \Closure|null  $before
     * @return static
     */
    public static function make($before = null)
    {
        return resolve(static::class)
            ->before($before);
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
     * @throws \RuntimeException
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

        throw new \RuntimeException(
            'The table must have a key column or a key property defined.'
        );
    }

    /**
     * Set the endpoint to be used for actions.
     *
     * @param  string  $endpoint
     * @return $this
     */
    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get the endpoint to be used for table actions.
     *
     * @return string
     */
    public function getEndpoint()
    {
        if (isset($this->endpoint)) {
            return $this->endpoint;
        }

        return static::fallbackEndpoint();
    }

    /**
     * Get the endpoint to be used for table actions from the config.
     *
     * @return string
     */
    public static function fallbackEndpoint()
    {
        return type(config('table.endpoint', '/actions'))->asString();
    }

    /**
     * Set whether the model attributes should serialized alongside columns.
     *
     * @param  bool|null  $attributes
     * @return $this
     */
    public function attributes($attributes = true)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get whether the model should be serialized per record.
     *
     * @return bool
     */
    public function hasAttributes()
    {
        if (isset($this->attributes)) {
            return $this->attributes;
        }

        return static::fallbackAttributes();
    }

    /**
     * Get whether the model should be serialized per record from the config.
     *
     * @return bool
     */
    public static function fallbackAttributes()
    {
        return (bool) config('table.attributes', false);
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
     * {@inheritdoc}
     */
    public static function fallbackDelimiter()
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
     * Handle the incoming action request for this table.
     *
     * @param  \Honed\Action\Http\Requests\ActionRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function handle($request)
    {
        return Handler::make(
            $this->getBuilder(),
            $this->getActions(),
            $this->getKey()
        )->handle($request);
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
    public function toArray()
    {
        $this->build();

        $id = $this->isWithoutActions() ? null : $this->getRouteKey();

        return \array_merge(parent::toArray(), [
            'id' => $id,
            'records' => $this->getRecords(),
            'paginator' => $this->getPaginationData(),
            'columns' => $this->columnsToArray(),
            'recordsPerPage' => $this->recordsPerPageToArray(),
            'toggleable' => $this->isToggleable(),
            'actions' => $this->actionsToArray(),
            'meta' => $this->getMeta(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configToArray()
    {
        return \array_merge(parent::configToArray(), [
            'endpoint' => $this->getEndpoint(),
            'record' => $this->getKey(),
            'records' => $this->formatScope($this->getRecordKey()),
            'columns' => $this->formatScope($this->getColumnsKey()),
            'pages' => $this->formatScope($this->getPageKey()),
        ]);
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
