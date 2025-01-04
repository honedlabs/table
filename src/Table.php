<?php

declare(strict_types=1);

namespace Honed\Table;

use Closure;
use Exception;
use RuntimeException;
use Honed\Core\Primitive;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Honed\Core\Concerns\Encodable;
use Honed\Core\Concerns\Inspectable;
use Illuminate\Support\Collection;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Columns\BaseColumn;
use Honed\Core\Concerns\IsAnonymous;
use Honed\Core\Concerns\RequiresKey;
use Honed\Table\Actions\InlineAction;
use Illuminate\Database\Eloquent\Model;
use Honed\Table\Http\DTOs\BulkActionData;
use Illuminate\Database\Eloquent\Builder;
use Honed\Table\Http\DTOs\InlineActionData;
use Honed\Table\Http\Requests\TableActionRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Honed\Core\Exceptions\MissingRequiredAttributeException;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Stringable;

/**
 * @template T of \Illuminate\Database\Eloquent\Model
 * @method static static build((\Closure(\Illuminate\Database\Eloquent\Builder<T>):(\Illuminate\Database\Eloquent\Builder<T>)|null) $resource = null) Build the table records and metadata using the current request.
 * @method $this build() Build the table records and metadata using the current request.
 */
class Table extends Primitive
{
    use Concerns\Filterable;
    use Concerns\HasRecords;
    use Concerns\HasActions;
    use Concerns\HasColumns;
    use Concerns\HasEndpoint;
    use Concerns\Searchable;
    use Concerns\Selectable;
    use Concerns\Sortable;
    use Concerns\HasResource;
    use Concerns\Toggleable;
    use Concerns\IsOptimizable;
    use Encodable;
    use IsAnonymous;
    use RequiresKey;

    /**
     * The parent class-string of the table.
     * 
     * @var class-string<\Honed\Table\Table>
     */
    protected $anonymous = self::class;

    /**
     * The request instance to use for the table.
     * 
     * @var \Illuminate\Http\Request|null
     */
    protected $request = null;

    /**
     * Build the table with the given assignments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>|T|class-string<T>|\Closure(\Illuminate\Database\Eloquent\Builder<T>):(\Illuminate\Database\Eloquent\Builder<T>)  $resource
     */
    public function __construct(Model|Builder|Closure|string $resource = null)
    {
        match (true) {
            \is_null($resource) => null,
            $resource instanceof Closure => $this->setResourceModifier($resource),
            default => $this->setResource($resource),
        };
    }

    /**
     * Dynamically handle calls to the class for enabling anonymous table methods.
     *
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        match ($method) {
            'actions' => $this->setActions(...$parameters),
            'build' => $this->configureTableForCurrentRequest(),
            'columns' => $this->setColumns(...$parameters),
            'filters' => $this->setFilters(...$parameters),
            'resource' => $this->setResource(...$parameters),
            'sorts' => $this->setSorts(...$parameters),
            // 'pages'
            // 'optimize'
            // 'reduce'
            default => parent::__call($method, $parameters)
        };

        return $this;
    }

    /**
     * Dynamically handle calls to the class for enabling static methods.
     *
     * @param  string  $method
     * @param  array<mixed>  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        return match ($method) {
            'build' => static::make(...$parameters)->build(),
            default => parent::__callStatic($method, $parameters)
        };
    }

    /**
     * Create a new table instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>|T|class-string<T>|\Closure(\Illuminate\Database\Eloquent\Builder<T>):(\Illuminate\Database\Eloquent\Builder<T>)  $resource
     */
    public static function make(Model|Builder|Closure|string $resource = null): static
    {
        return resolve(static::class, compact('resource'));
    }

    /**
     * Get the key name for the table records.
     *
     * @throws \Honed\Core\Exceptions\MissingRequiredAttributeException
     */
    public function getKeyName(): string
    {
        try {
            return $this->getKey();
        } catch (MissingRequiredAttributeException $e) {
            return $this->getKeyColumn()?->getName() ?? throw $e;
        }
    }

    /**
     * Get the table as an array.
     * 
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $this->configureTableForCurrentRequest();

        return [
            /* The ID of this table, used to deserialize it for actions */
            'id' => $this->encodeClass(),
            /* The column attribute used to identify a record */
            'key' => $this->getKeyName(),
            /* The records of the table */
            'records' => $this->getRecords(),
            /* The available column options */
            'columns' => $this->getColumns(),
            /* The available bulk action options */
            'bulkActions' => $this->getBulkActions(),
            /* The available page action options, generally page links */
            'pageActions' => $this->getPageActions(),
            /* The available filter options */
            'filters' => $this->getFilters(),
            /* The available sort options */
            'sorts' => $this->getSorts(),
            /* The pagination data for the records */
            'paginator' => $this->getPaginator(),
            /* Whether the table has toggling enabled */
            'toggleable' => $this->isToggleable(),
            /* The query parameter term for sorting */
            'sort' => $this->getSortKey(),
            /* The query parameter term for ordering */
            'order' => $this->getOrderKey(),
            /* The query parameter term for changing the number of records per page */
            'count' => $this->getCountKey(),
            /* The query parameter term for searching */
            'search' => $this->getSearchKey(),
            /* The query parameter term for toggling column visibility */
            'toggle' => $this->getToggleKey(),
            /* The route used to handle actions, it is required to be a 'post' route */
            'endpoint' => $this->getEndpoint(),
        ];
    }

    /**
     * Build the table records and metadata using the current request.
     */
    protected function configureTableForCurrentRequest(): void
    {
        if ($this->hasRecords()) {
            return;
        }

        $this->modifyResource();
        $this->setSearchColumns();
        $this->toggleColumns();
        $this->filterQuery($this->getResource());
        $this->sortQuery($this->getResource());
        $this->searchQuery($this->getResource());
        // $this->selectQuery($this->getQuery());
        $this->beforeRetrieval();
        // $this->formatRecords();
        // $this->paginateRecords();
    }

    protected function modifyResource(): void
    {
        if ($this->hasResourceModifier()) {
            \call_user_func($this->getResourceModifier(), $this->resource);
        }
    }

    protected function beforeRetrieval(): void
    {
        if (\method_exists($this, 'before')) {
            \call_user_func($this->getResourceModifier(), $this->resource);
        }
    }

    protected function setSearchColumns(): void
    {
        $searchProperty = (array) $this->getSearch();

        $searchColumns = $this->getColumns()
            ->filter(static fn (BaseColumn $column): bool => $column->isSearchable())
            ->pluck('name')
            ->all();

        // Override the search property with the unique combination of the property and columns
        $this->setSearch(\array_unique([...$searchProperty, ...$searchColumns]));
    }

    protected function toggleColumns(): void
    {
        $cols = $this->getToggledColumns(); // names

        if (empty($cols)) {
            return;
        }

        $this->getColumns()->each(function (BaseColumn $column) use ($cols) {
            if (\in_array($column->getName(), $cols)) {
                $column->setActive(true);
            } else {
                $column->setActive(false);
            }
        });
    }

    /**
     * Global handler for piping the request to the correct table action handler.
     */
    public function handleAction(TableActionRequest $request)
    {
        $result = match ($request->validated('type')) {
            'inline' => $this->executeInlineAction(InlineActionData::from($request)),
            'bulk' => $this->executeBulkAction(BulkActionData::from($request)),
            default => abort(404)
        };

        if ($result instanceof Response) {
            return $result;
        }

        return back();
    }

    /**
     * Execute a given inline action of the table.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function executeInlineAction(InlineActionData $data)
    {
        $action = $this->getInlineActions()->first(fn (InlineAction $action) => $action->getName() === $data->name);

        if (\is_null($action)) {
            throw new \Exception('Invalid action');
        }

        $record = $this->resolveModel($data->id);

        // Ensure that the user is authorized to perform the action on this model
        if (! $action->isAuthorized([
            'record' => $record,
            $this->getModelClassName() => $record,
        ], [
            (string) $this->getModelClass() => $record,
            Model::class => $record,
        ])) {
            throw new \Exception('Unauthorized');
        }

        return $this->evaluate(
            value: $action->getAction(),
            named: [
                'record' => $record,
                'model' => $record,
                $this->getModelClassName() => $record,
            ],
            typed: [
                (string) $this->getModelClass() => $record,
                // Model::class => $record,
            ],
        );
    }

    /**
     * Execute a given bulk action of the table.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function executeBulkAction(BulkActionData $data)
    {
        $action = $this->getBulkActions()->first(fn (BulkAction $action) => $action->getName() === $data->name);

        if (\is_null($action)) {
            throw new Exception('Invalid action');
        }

        if (! $action->isAuthorized()) {
            throw new Exception('Unauthorized');
        }

        $key = $this->getKey();

        $query = $this->getResource();
        $query = match (true) {
            $data->all => $query->whereNotIn($key, $data->except),
            default => $query->whereIn($key, $data->only)
        };

        $reflection = new \ReflectionFunction($action->getAction());
        $hasRecordsParameter = collect($reflection->getParameters())
            ->some(fn (\ReflectionParameter $parameter) => $parameter->getName() === 'records'
                || ($parameter->getType() instanceof \ReflectionNamedType && $parameter->getType()->getName() === Collection::class)
            );

        return $this->evaluate(
            value: $action->getAction(),
            named: [
                'query' => $query,
                ...($hasRecordsParameter ? ['records' => $query->get()] : []),
            ],
            typed: [
                Builder::class => $query,
                ...($hasRecordsParameter ? [Collection::class => $query->get()] : []),
            ],
        );
    }
}
