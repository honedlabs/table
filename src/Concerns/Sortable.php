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
    public const SortKey = 'sort';

    public const OrderKey = 'order';

    /**
     * @var string
     */
    // protected $sort;

    /**
     * @var string
     */
    protected static $sortKey = self::SortKey;

    /**
     * @var string
     */
    // protected $order;

    /**
     * @var string
     */
    protected static $orderKey = self::OrderKey;

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
     * Determine if the class has sorts.
     */
    public function hasSorts(): bool
    {
        return $this->getSorts()->isNotEmpty();
    }

    /**
     * Get the sorts to apply to the resource.
     *
     * @return Collection<\Honed\Table\Sorts\BaseSort>
     */
    public function getSorts(): Collection
    {
        return collect(match(true) {
            \property_exists($this, 'sorts') => $this->sorts,
            \method_exists($this, 'sorts') => $this->sorts(),
            default => [],
        });
    }

    /**
     * Configure the default query parameter to use for sorting.
     *
     * @return void
     */
    public static function useSortKey(string $sortKey)
    {
        static::$sortKey = $sortKey;
    }

    /**
     * Configure the default query parameter to use for ordering.
     *
     * @return void
     */
    public static function useOrderKey(string $orderKey)
    {
        static::$orderKey = $orderKey;
    }

    /**
     * Get the query parameter to use for sorting.
     *
     * @return string
     */
    public function getSortKey()
    {
        return \property_exists($this, 'sort')
            ? $this->sort
            : static::$sortKey;
    }

    /**
     * Get the query parameter to use for ordering.
     *
     * @return string
     */
    public function getOrderKey()
    {
        return \property_exists($this, 'order')
            ? $this->order
            : static::$orderKey;
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
        $sortBy = $request->string($this->getSortKey())->toString();
        $sortBy = $sortBy === '' ? null : $sortBy;

        $sortDirection = null;

        // Extract direction prefix if present
        if (! \is_null($sortBy) && str($sortBy)->startsWith(['+', '-'])) {
            $sortDirection = str($sortBy)->startsWith('+') ? 'asc' : 'desc';
            $sortBy = str($sortBy)->substr(1)->toString();
            $sortBy = $sortBy === '' ? null : $sortBy;
        }

        // Get direction from query param or use the one from prefix
        $direction = $request->string($this->getOrderKey())->toString();
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
