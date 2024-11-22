<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Primitive;
use BadMethodCallException;
use Honed\Table\Pipes\Paginate;
use Honed\Table\Concerns\HasMeta;
use Honed\Table\Pipes\ApplySorts;
use Illuminate\Pipeline\Pipeline;
use Honed\Table\Concerns\Encodable;
use Honed\Table\Pipes\ApplyFilters;
use Honed\Table\Pipes\ApplyToggles;
use Honed\Core\Concerns\Inspectable;
use Honed\Core\Concerns\IsAnonymous;
use Honed\Core\Concerns\RequiresKey;
use Honed\Table\Pipes\FormatRecords;
use Honed\Table\Pipes\OptimalSelect;
use Honed\Table\Pipes\ApplyBeforeRetrieval;
use Honed\Core\Exceptions\MissingRequiredAttributeException;

class Table extends Primitive
{
    use Inspectable;
    use Encodable;
    use RequiresKey {
        getKey as protected getTableKey;
    }
    use IsAnonymous;
    use Concerns\HasResource;
    use Concerns\HasActions;
    use Concerns\HasColumns;
    use Concerns\HasFilters;
    use Concerns\Pageable;
    use Concerns\Sortable;
    use Concerns\Toggleable;
    use Concerns\Searchable;
    use Concerns\Records; // Records
    use Concerns\Selectable;
    use HasMeta; // -> Remove

    /**
     * Check if the table is built in-line.
     * 
     * @var class-string<\Honed\Table\Table>
     */
    protected $anonymous = Table::class;

    /**
     * Build the table with the given assignments.
     * 
     * @param array<string, mixed> $assignments
     */
    public function __construct($assignments = [])
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
    public static function make($resource = null,
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

    public function toArray()
    {
        $this->pipeline();

        dd(static::class);
        return [
            /* The table id used to deserialize it for actions */
            'id' => $this->encodeClass(),
            /* The records of the table */
            'records' => $this->records,
            /* The pagination data for the records */
            'meta' => $this->meta,
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
            'keys' => [
                'id' => $this->getKey(),
                'sort' => $this->getSortAs(),
                'order' => $this->getOrderAs(),
                'count' => $this->getCountAs(),
                'search' => $this->getSearchAs(),
                'toggle' => $this->getToggleAs(),
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
     * Dynamically handle calls to the class for handling anonymous table methods.
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

    
    // Table::register('/table'); -> alias for Route::post('/table/{table}', ActionHandler::class);
        // But must first do the model binding such that it can be resolved from the container
    // Table::router(); -> registers the default routes for the table
}
