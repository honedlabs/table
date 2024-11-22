<?php

declare(strict_types=1);

namespace Honed\Table;

use Exception;
use Honed\Core\Primitive;
use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Honed\Table\Pipes\Paginate;
use Honed\Table\Concerns\HasMeta;
use Honed\Table\Pipes\ApplySorts;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Honed\Table\Actions\BaseAction;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Concerns\Encodable;
use Honed\Table\Pipes\ApplyFilters;
use Honed\Table\Pipes\ApplyToggles;
use Honed\Core\Concerns\Inspectable;
use Honed\Core\Concerns\IsAnonymous;
use Honed\Core\Concerns\RequiresKey;
use Honed\Table\Pipes\FormatRecords;
use Honed\Table\Pipes\OptimalSelect;
use Honed\Table\Actions\InlineAction;
use Illuminate\Database\Eloquent\Model;
use Honed\Table\Http\DTOs\BulkActionData;
use Illuminate\Database\Eloquent\Builder;
use Honed\Table\Http\DTOs\InlineActionData;
use Honed\Table\Pipes\ApplyBeforeRetrieval;
use Honed\Table\Http\Requests\TableActionRequest;
use Honed\Core\Exceptions\MissingRequiredAttributeException;

class Table extends Primitive
{
    use Inspectable;
    use Encodable;
    use RequiresKey {
        getKey as protected getTableKey;
    }
    use IsAnonymous;
    use Concerns\Resourceful;
    use Concerns\HasActions;
    use Concerns\HasColumns;
    use Concerns\HasFilters;
    use Concerns\Pageable;
    use Concerns\Sortable;
    use Concerns\Toggleable;
    use Concerns\Searchable;
    use Concerns\Selectable;
    use Concerns\EnforceableColumns;

    /**
     * @var \Illuminate\Support\Collection<array-key, array<array-key, mixed>>|null
     */
    protected $records = null;

    /**
     * @var array<string, mixed>
     */
    protected array $meta = [];

    /**
     * @var class-string<\Honed\Table\Table>
     */
    protected $anonymous = self::class;

    /**
     * Build the table with the given assignments.
     * 
     * @param array<string, mixed> $assignments
     */
    final public function __construct($assignments = [])
    {
        $this->setAssignments($assignments);
    }

    /**
     * Create a new table instance.
     * 
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|class-string $resource
     * @param array<\Honed\Table\Columns\BaseColumn> $columns
     * @param array<\Honed\Table\Actions\BaseAction> $actions
     * @param array<\Honed\Table\Filters\BaseFilter> $filters
     * @param array<\Honed\Table\Sorts\BaseSort> $sorts
     * @param string|null $search
     * @param array|int|null $pagination
     * @return static
     */
    final public static function make($resource = null,
        $columns = null,
        $actions = null,
        $filters = null,
        $sorts = null,
        $search = null,
        $pagination = null,
    ) {
        return resolve(static::class, compact(
            'resource',
            'columns',
            'actions',
            'filters',
            'sorts',
            'search',
            'pagination',
        ));
    }

    /**
     * Set the records for the table.
     * 
     * @param \Illuminate\Support\Collection<array-key, array<array-key, mixed>> $records
     */
    public function setRecords($records): void
    {
        $this->records = $records;
    }

    /**
     * Set the meta data for the table.
     *
     * @param array<string, mixed>|null $meta
     */
    public function setMeta($meta)
    {
        if (empty($meta)) {
            return;
        }
        $this->meta = $meta;
    }

    /**
     * Get the key for the table records.
     *
     * @throws MissingRequiredAttributeException
     * @return string
     */
    public function getKey()
    {
        try {
            return $this->getTableKey();
        } catch (MissingRequiredAttributeException $e) {
            return $this->getKeyColumn()?->getName() ?? throw $e;
        }
    }

    public function getRecords(): ?Collection
    {
        return $this->records;
    }

    public function hasRecords(): bool
    {
        return !\is_null($this->records);
    }

    /**
     * Get the meta data for the table.
     *
     * @return array<string, mixed>
     */
    public function getMeta()
    {
        return $this->meta;
    }

    public function toArray()
    {
        $this->pipeline();

        return [
            /* The table id used to deserialize it for actions */
            'id' => $this->encodeClass(),
            /* The records of the table */
            'records' => $this->getRecords(),
            /* The pagination data for the records */
            'meta' => $this->getMeta(),
            /* The available sort options */
            'sorts' => $this->getSorts(),
            /* The available filter options */
            'filters' => $this->getFilters(),
            /* The available column options */
            'columns' => $this->getColumns(),
            /* The pagination counter */
            'pagination' => $this->getPaginationCounts($this->getPageCount()),
            /* The available action options -> bulk/inline only for non-anonymous tables */
            'actions' => [
                'inline' => $this->getInlineActions(),
                'bulk' => $this->getBulkActions(),
                'page' => $this->getPageActions(),
            ],
            /* Track the keys used to make requests to identify changes to this specific table */
            'config' => [
                'record_id' => $this->getKey(),
                'sort' => $this->getSortAs(),
                'order' => $this->getOrderAs(),
                'count' => $this->getCountAs(),
                'search' => $this->getSearchAs(),
                'toggle' => $this->getToggleAs(),
                // 'route'
            ],
        ];
    }

    /**
     * Build the table records and metadata using the current request.
     *
     * @internal
     */
    protected function pipeline(): void
    {
        if ($this->hasRecords()) {
            return;
        }

        app(Pipeline::class)->send($this)
            ->through([
                ApplyToggles::class,
                ApplyFilters::class,
                // ApplySearch::class,
                ApplySorts::class,
                OptimalSelect::class,
                ApplyBeforeRetrieval::class,
                Paginate::class,
                FormatRecords::class,
            ])
            ->via('handle')
            ->thenReturn();
    }

    /**
     * Dynamically handle calls to the class for enabling anonymous table methods.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        match ($method) {
            'actions' => $this->setActions(...$parameters),
            'columns' => $this->setColumns(...$parameters),
            'filters' => $this->setFilters(...$parameters),
            'sorts' => $this->setSorts(...$parameters),
            default => parent::__call($method, $parameters)
        };

        return $this;
    }

    /**
     * Global handler for piping the request to the correct table action handler.
     */
    public function handleAction(TableActionRequest $request)
    {
        $result = match ($request->validated('type')) {
            'inline' => $this->executeInlineAction(InlineActionData::from($request)),
            'bulk' => $this->executeBulkAction(BulkActionData::from($request)),
        };

        if ($result instanceof Response) {
            return $result;
        }

        return back();
    }

    /**
     * Execute a given inline action of the table.
     * 
     * @param \Honed\Table\Http\DTOs\InlineActionData $data
     * @return mixed
     * @throws \Exception
     */
    protected function executeInlineAction(InlineActionData $data)
    {
        $action = $this->getInlineActions()->first(fn (InlineAction $action) => $action->getName() === $data->name);

        if (\is_null($action)) {
            throw new Exception('Invalid action');
        }

        $record = $this->resolveModel($data->id);

        // Ensure that the user is authorized to perform the action on this model
        if (!$action->isAuthorized($record)) {
            throw new Exception('Unauthorized');
        }

        return $this->evaluate(
            value: $action->getAction(),
            named: [
                'record' => $record,
                'model' => $record,
                $this->getModelClassName() => $record,
            ],
            typed: [
                Model::class => $record,
                $this->getModelClass() => $record,
            ],
        );
    }

    /**
     * Execute a given bulk action of the table.
     * 
     * @param \Honed\Table\Http\DTOs\BulkActionData $data
     * @return mixed
     * @throws \Exception
     */
    protected function executeBulkAction(BulkActionData $data)
    {
        $action = $this->getBulkActions()->first(fn (BulkAction $action) => $action->getName() === $data->name);

        if (\is_null($action)) {
            throw new Exception('Invalid action');
        }

        if (!$action->isAuthorized()) {
            throw new Exception('Unauthorized');
        }

        $key = $this->getKey();

        $query = $this->getResourceQuery();
        $query = match (true) {
            $data->all => $query->whereNotIn($key, $data->except),
            default => $query->whereIn($key, $data->only)
        };

        $reflection = new \ReflectionFunction($action->getAction());
        $hasRecordsParameter = collect($reflection->getParameters())
            ->some(fn (\ReflectionParameter $parameter) => 'records' === $parameter->getName() 
                || Collection::class === $parameter->getType()
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
