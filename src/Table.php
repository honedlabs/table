<?php

declare(strict_types=1);

namespace Conquest\Table;

use BadMethodCallException;
use Conquest\Core\Primitive;
use App\Table\Pipes\Paginate;
use App\Table\Pipes\ApplySorts;
use App\Table\Pipes\ApplySearch;
use App\Table\Pipes\ApplyFilters;
use Illuminate\Pipeline\Pipeline;
use App\Table\Pipes\FormatRecords;
use Conquest\Table\Concerns\HasMeta;
use Conquest\Table\Concerns\HasSort;
use Conquest\Table\Concerns\HasOrder;
use Conquest\Table\Concerns\HasSorts;
use Conquest\Table\Concerns\EncodesId;
use Conquest\Table\Pipes\ApplyToggles;
use Conquest\Core\Concerns\IsAnonymous;
use Conquest\Core\Concerns\RequiresKey;
use Conquest\Table\Concerns\HasActions;
use Conquest\Table\Concerns\HasColumns;
use Conquest\Table\Concerns\HasFilters;
use Conquest\Table\Concerns\HasRecords;
use Conquest\Table\Concerns\HasResource;
use Illuminate\Database\Eloquent\Builder;
use Conquest\Table\Concerns\Remember\Remembers;
use Conquest\Table\Pagination\Concerns\Paginates;
use Conquest\Core\Exceptions\MissingRequiredAttributeException;
use Conquest\Table\Concerns\CanSearch;
use Conquest\Table\Concerns\HasSearchAs;
use Conquest\Table\Concerns\Search\HasSearch;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
class Table extends Primitive
{
    /** Infrastructure traits */
    use EncodesId;
    use RequiresKey {
        getKey as protected getInternalKey;
    }
    use HasActions;
    use HasColumns;
    use HasFilters;
    use HasMeta;
    use HasRecords;
    use HasResource;
    use IsAnonymous;
    use Paginates;
    /** Toggle traits */
    use Remembers;
    /** Sort traits */
    use HasSorts;
    use HasOrder;
    use HasSort;
    /** Search traits */
    use HasSearch;
    use HasSearchAs;
    use CanSearch;

    /**
     * Check if the table is built in-line.
     * 
     * @var class-string<\Conquest\Table\Table>
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
     * @param array<string, \Conquest\Table\Columns\BaseColumn> $columns
     * @param array<string, \Conquest\Table\Actions\BaseAction> $actions
     * @param array<string, \Conquest\Table\Filters\BaseFilter> $filters
     * @param array<string, \Conquest\Table\Sorts\BaseSort> $sorts
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
            return $this->getInternalKey();
        } catch (MissingRequiredAttributeException $e) {
            return $this->getKeyColumn()?->getName() ?? throw $e;
        }
    }

    /**
     * Retrieve the table as an array
     */
    public function toArray()
    {
        $this->pipeline();

        return [
            'id' => $this->getEncodedId($this->getId()),
            'records' => $this->records,
            'meta' => $this->meta,
            'sorts' => $this->getSorts(),
            'filters' => $this->getFilters(),
            'columns' => $this->getColumns(),
            'pagination' => $this->getPagination($this->usePerPage()),
            'actions' => [
                'inline' => $this->getInlineActions(),
                'bulk' => $this->getBulkActions(),
                'page' => $this->getPageActions(),
                'default' => $this->getDefaultAction(),
            ],
            'keys' => [
                'id' => $this->getKey(),
                'sort' => $this->getSort(),
                'order' => $this->getOrder(),
                'show' => $this->getShowKey(),
                'search' => $this->getSearch(),
                'toggle' => $this->getToggleKey(),
            ],
        ];
    }

    /**
     * Retrieve the records and table metadata.
     *
     * @internal
     */
    protected function pipeline()
    {
        if ($this->hasRecords()) {
            return;
        }

        app(Pipeline::class)->send($this)
            ->through([
                ApplyToggles::class,
                ApplyFilters::class,
                ApplySearch::class,
                ApplySorts::class,
                Paginate::class,
                FormatRecords::class,
            ])
            ->via('handle')
            ->thenReturn();
    }

    /**
     * Dynamically handle calls to the class and handle anonymous table methods.
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
