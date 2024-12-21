<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Honed\Table\Sorts\BaseSort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait Sortable
{
    public const DefaultSortKey = 'sort';

    public const DefaultOrderKey = 'order';

    /**
     * @var array<int,\Honed\Table\Sorts\BaseSort>
     */
    protected $sorts;

    /**
     * @var string
     */
    protected $sort;

    /**
     * @var string
     */
    protected static $sortName = self::DefaultSortKey;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var string
     */
    protected static $orderName = self::DefaultOrderKey;

    /**
     * Set the list of sorts to apply to a query.
     *
     * @param  array<int, \Honed\Table\Sorts\BaseSort>|null  $sorts
     */
    public function setSorts(?array $sorts): void
    {
        if (\is_null($sorts)) {
            return;
        }

        $this->sorts = $sorts;
    }

    /**
     * Determine if the class has no sorts.
     */
    public function missingSorts(): bool
    {
        return $this->getSorts()->isEmpty();
    }

    /**
     * Determine if the class has sorts.
     */
    public function hasSorts(): bool
    {
        return ! $this->missingSorts();
    }

    /**
     * Get the sorts to apply to the resource.
     *
     * @return Collection<\Honed\Table\Sorts\BaseSort>
     */
    public function getSorts(): Collection
    {
        return collect($this->inspect('sorts', []));
    }

    /**
     * Configure the default query parameter to use for sorting.
     *
     * @return void
     */
    public static function sortName(string $sortName)
    {
        static::$sortName = $sortName;
    }

    /**
     * Configure the default query parameter to use for ordering.
     *
     * @return void
     */
    public static function orderName(string $orderName)
    {
        static::$orderName = $orderName;
    }

    /**
     * Get the query parameter to use for sorting.
     *
     * @return string
     */
    public function getSortName()
    {
        return $this->inspect('sort', static::$sortName);
    }

    /**
     * Get the query parameter to use for ordering.
     *
     * @return string
     */
    public function getOrderName()
    {
        return $this->inspect('order', static::$orderName);
    }

    /**
     * Retrieve the sort value and direction from the current request.
     *
     * @return array{string|null,'asc'|'desc'|null} [sort field, direction]
     */
    public function getSortParameters(?Request $request = null): array
    {
        $request = $request ?? request();

        // Get the raw sort value, ensuring null if empty
        $sortBy = $request->string($this->getSortName())->toString();
        $sortBy = $sortBy === '' ? null : $sortBy;

        $sortDirection = null;

        // Extract direction prefix if present
        if (! \is_null($sortBy) && str($sortBy)->startsWith(['+', '-'])) {
            $sortDirection = str($sortBy)->startsWith('+') ? 'asc' : 'desc';
            $sortBy = str($sortBy)->substr(1)->toString();
            $sortBy = $sortBy === '' ? null : $sortBy;
        }

        // Get direction from query param or use the one from prefix
        $direction = $request->string($this->getOrderName())->toString();
        $direction = match (strtolower($direction ?: $sortDirection ?: '')) {
            'asc' => 'asc',
            'desc' => 'desc',
            default => null,
        };

        return [$sortBy, $direction];
    }

    /**
     * Apply the sorts to a query using the current request
     */
    public function sortQuery(Builder $builder): void
    {
        [$sortBy, $direction] = $this->getSortParameters();

        // Get the column sorts as well as column
        $columnSorts = $this->getColumns()->map(fn (BaseColumn $column) => $column->getSort())->filter();

        $sorts = $this->getSorts()->merge($columnSorts);

        // Need to handle defaults case
        $sorts->each(static fn (BaseSort $sort) => $sort->apply($builder, $sortBy, $direction));
    }
}
