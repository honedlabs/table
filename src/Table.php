<?php

declare(strict_types=1);

namespace Honed\Table;

use Closure;
use Exception;
use Honed\Core\Concerns\Encodable;
use Honed\Core\Concerns\IsAnonymous;
use Honed\Core\Concerns\RequiresKey;
use Honed\Core\Exceptions\MissingRequiredAttributeException;
use Honed\Core\Primitive;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Columns\BaseColumn;
use Honed\Table\Http\DTOs\BulkActionData;
use Honed\Table\Http\DTOs\InlineActionData;
use Honed\Table\Http\Requests\TableActionRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * @template T of \Illuminate\Database\Eloquent\Model
 *
 * @method static static build((\Closure(\Illuminate\Database\Eloquent\Builder<T>):(\Illuminate\Database\Eloquent\Builder<T>)|null) $resource = null) Build the table records and metadata using the current request.
 * @method $this build() Build the table records and metadata using the current request.
 */
class Table extends Primitive
{
    use Concerns\ActsBeforeRetrieval;
    use Concerns\Filterable;
    use Concerns\HasActions;
    use Concerns\HasColumns;
    use Concerns\HasEndpoint;
    use Concerns\HasPages;
    use Concerns\HasRecords;
    use Concerns\HasResource;
    use Concerns\HasResourceModifier;
    use Concerns\IsOptimizable;
    use Concerns\Searchable;
    use Concerns\Selectable;
    use Concerns\Sortable;
    use Concerns\Toggleable;
    use Encodable;
    use IsAnonymous;
    use RequiresKey;

    /**
     * The parent class-string of the table.
     *
     * @var class-string<\Honed\Table\Table<T>>
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
    public function __construct(Model|Builder|Closure|string|null $resource = null)
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
    public static function make(Model|Builder|Closure|string|null $resource = null): static
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
            'id' => $this->encodeClass(),
            'endpoint' => $this->isAnonymous() ? null : $this->getEndpoint(),
            'toggleable' => $this->isToggleable(),
            'keys' => [
                'records' => $this->getKeyName(),
                'sorts' => $this->getSortKey(),
                'order' => $this->getOrderKey(),
                'search' => $this->getSearchKey(),
                'toggle' => $this->getToggleKey(),
                'shown' => $this->getShownKey(),
            ],
            'records' => $this->getRecords(),
            'columns' => $this->getColumns(),
            'actions' => [
                'bulk' => $this->getBulkActions(),
                'page' => $this->getPageActions(),
            ],
            'filters' => $this->getFilters(),
            'sorts' => $this->getSorts(),
            'pages' => $this->getPages(),
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

        $columns = $this->getColumns();
        $activeColumns = $this->toggleColumns($columns);
        $resource = $this->getResource();

        $this->modifyResource($resource);
        $this->filterQuery($resource);
        $this->sortQuery($resource);
        $this->searchQuery($resource, $columns);
        $this->optimizeQuery($resource, $activeColumns);
        $this->beforeRetrieval($resource);

        $records = $this->paginateRecords($resource);
        $formatted = $this->formatRecords($records, $activeColumns, $this->getInlineActions(), $this->getSelector());
        $this->setRecords($formatted);
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
            $this->getModelName() => $record,
        ], [
            (string) $this->getModel() => $record,
            Model::class => $record,
        ])) {
            throw new \Exception('Unauthorized');
        }

        return $this->evaluate(
            value: $action->getAction(),
            named: [
                'record' => $record,
                'model' => $record,
                strtolower($this->getModelName()) => $record,
            ],
            typed: [
                (string) $this->getModel() => $record,
                Model::class => $record,
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
