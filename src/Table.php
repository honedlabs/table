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
use Conquest\Table\Concerns\Sorts;
use Conquest\Table\Concerns\HasMeta;
use Conquest\Table\Pipes\SetActions;
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
use Conquest\Table\Concerns\Search\Searches;
use Conquest\Table\Concerns\Remember\Remembers;
use Conquest\Table\Pagination\Concerns\Paginates;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Conquest\Core\Exceptions\MissingRequiredAttributeException;

class Table extends Primitive
{
    use EncodesId;
    use HasActions;
    use HasColumns;
    use HasFilters;
    use HasMeta;
    use HasRecords;
    use HasResource;
    use IsAnonymous;
    use Paginates;
    use Remembers;
    use RequiresKey {
        getKey as protected getInternalKey;
    }
    use Searches;
    use Sorts;

    /**
     * Check if the table is built in-line.
     * 
     * @var class-string<Table>
     */
    protected $anonymous = Table::class;

    public function __construct(array $assignments = [])
    {
        $this->setAssignments($assignments);
    }

    /**
     * Create a new table instance.
     * 
     * @param Builder|Model|class-string $resource
     * @param array<string, BaseColumn> $columns
     * @param array<string, BaseAction> $actions
     * @param array<string, BaseFilter> $filters
     * @param array<string, BaseSort> $sorts
     * @param string|null $search
     * @param array|int|null $pagination
     */
    public static function make($resource = null,
        $columns = null,
        $actions = null,
        $filters = null,
        $sorts = null,
        $search = null,
        $pagination = null,
    ): static {
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
    public function getKey(): string
    {
        try {
            return $this->getInternalKey();
        } catch (MissingRequiredAttributeException $e) {
            return $this->getKeyColumn()?->getName() ?: $this->getResourceModel()->getKeyName();
        }
    }

    /**
     * Retrieve the table as an array
     */
    public function toArray(): array
    {
        $this->pipeline();

        return [
            'id' => $this->getEncodedId($this->getId()),
            'records' => $this->records,
            'headings' => $this->getHeadingColumns(),
            'meta' => $this->meta,
            'sorts' => $this->getSorts(),
            'filters' => $this->getFilters(),
            'columns' => $this->getTableColumns(),
            'pagination' => $this->getPagination($this->usePerPage()),
            'actions' => [
                'inline' => $this->getInlineActions(),
                'bulk' => $this->getBulkActions(),
                'page' => $this->getPageActions(),
                'default' => $this->getDefaultAction(),
            ],
            'keys' => [
                'id' => $this->getTableKey(),
                'sort' => $this->getSortKey(),
                'order' => $this->getOrderKey(),
                'show' => $this->getShowKey(),
                'post' => $this->getActionRoute(),
                'search' => $this->getSearchKey(),
                'toggle' => $this->getToggleKey(),
            ],
        ];
    }

    /**
     * Retrieve the records and table metadata.
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
                ApplySearch::class,
                ApplySorts::class,
                Paginate::class,
                FormatRecords::class,
            ])
            ->via('handle')
            ->thenReturn();
    }

    public function __call(string $method, array $parameters)
    {
        match ($method) {
            'actions' => $this->setActions(...$parameters),
            'columns' => $this->setColumns(...$parameters),
            'filters' => $this->setFilters(...$parameters),
            'sorts' => $this->setSorts(...$parameters),
            default => throw new BadMethodCallException(sprintf('Method %s does not exist on [%s].', $method, static::class))
        };

        return $this;
    }
}
