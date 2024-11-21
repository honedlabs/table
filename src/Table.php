<?php

declare(strict_types=1);

namespace Honed\Table;

use BadMethodCallException;
use Honed\Core\Primitive;
use Honed\Table\Pipes\Paginate;
use Honed\Table\Pipes\ApplySorts;
use Honed\Table\Pipes\ApplySearch;
use Honed\Table\Pipes\ApplyFilters;
use Illuminate\Pipeline\Pipeline;
use Honed\Table\Pipes\FormatRecords;
use Honed\Core\Concerns\Inspectable;
use Honed\Table\Concerns\HasMeta;
use Honed\Table\Concerns\HasSort;
use Honed\Table\Concerns\HasOrder;
use Honed\Table\Concerns\HasSorts;
use Honed\Table\Concerns\EncodesId;
use Honed\Table\Pipes\ApplyToggles;
use Honed\Core\Concerns\IsAnonymous;
use Honed\Core\Concerns\RequiresKey;
use Illuminate\Database\Eloquent\Builder;
use Honed\Table\Concerns\Remember\Remembers;
use Honed\Table\Pagination\Concerns\Paginates;
use Honed\Core\Exceptions\MissingRequiredAttributeException;
use Honed\Table\Concerns\CanSearch;
use Honed\Table\Concerns\HasSearchAs;
use Honed\Table\Concerns\Search\HasSearch;
use Illuminate\Database\Eloquent\Model;

class Table extends Primitive
{
    use Inspectable;
    use EncodesId; // Encodable
    use RequiresKey {
        getKey as protected getTableKey;
    }
    use Concerns\HasResource;
    use Concerns\HasActions;
    use Concerns\HasColumns;
    use Concerns\HasFilters;
    use HasMeta; // -> Remove
    use Concerns\Records; // Records
    // use IsAnonymous;
    use Paginates; // Pageable
    /** Toggle traits -> toggleable */
    use Remembers;
    /** Sort traits -> sortable*/
    use HasSorts;
    use HasOrder;
    use HasSort;
    // use Concerns\Searchable;
    // use Concerns\Toggleable;
    // use Concerns\Sortable;
    // use Concerns\Records;
    // use Concerns\Encodable;
    /** Search traits -> searchable */
    // use HasSearch;
    // use HasSearchAs;
    // use CanSearch;

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

        return [
            /* The table id used to deserialize it for actions */
            'id' => $this->getEncodedId($this->getId()),
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
            'pagination' => $this->getPagination($this->usePerPage()),
            /* The available action options -> bulk/inline only for non-anonymous tables */
            'actions' => [
                'inline' => $this->getInlineActions(),
                'bulk' => $this->getBulkActions(),
                'page' => $this->getPageActions(),
            ],
            /* Track the keys used to make requests to identify changes to this specific table */
            'keys' => [
                'id' => $this->getKey(),
                'sort' => $this->getSort(),
                'order' => $this->getOrder(),
                'show' => $this->getShowKey(),
                // 'search' => $this->getSearch(),
                'toggle' => $this->getToggleKey(),
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
