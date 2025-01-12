<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Honed\Table\Sorts\Contracts\Sort;
use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    const SortKey = 'sort';

    const OrderKey = 'order';

    /**
     * @var array<int,\Honed\Table\Sorts\Contracts\Sort>
     */
    protected $sorts;

    /**
     * @var string
     */
    protected $sort;

    /**
     * @var string
     */
    protected static $sortKey = self::SortKey;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var string
     */
    protected static $orderKey = self::OrderKey;

    /**
     * Configure the default query parameter to use for sorting.
     */
    public static function useSortKey(?string $sortKey = null): void
    {
        static::$sortKey = $sortKey ?? self::SortKey;
    }

    /**
     * Configure the default query parameter to use for ordering.
     */
    public static function useOrderKey(?string $orderKey = null): void
    {
        static::$orderKey = $orderKey ?? self::OrderKey;
    }

    /**
     * Set the list of sorts to apply to a query.
     *
     * @param  array<int, \Honed\Table\Sorts\Contracts\Sort>|null  $sorts
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
     * Get the sorts to apply to a resource.
     *
     * @return Collection<\Honed\Table\Sorts\Contracts\Sort>
     */
    public function getSorts(): Collection
    {
        return collect(match(true) {
            \property_exists($this, 'sorts') && !\is_null($this->sorts) => $this->sorts,
            \method_exists($this, 'sorts') => $this->sorts(),
            default => [],
        });
    }

    /**
     * Get the query parameter to use for sorting.
     *
     * @return string
     */
    public function getSortKey()
    {
        return \property_exists($this, 'sort') && !\is_null($this->sort)
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
        return \property_exists($this, 'order') && !\is_null($this->order)
            ? $this->order
            : static::$orderKey;
    }

    /**
     * Retrieve the sort value and direction from the current request.
     *
     * @return array{string|null,string|null} [sort field, direction]
     */
    public function getSortParameters(?Request $request = null): array
    {
        $request = $request ?? request();

        $sortBy = $request->input($this->getSortKey(), null);
        $direction = $request->input($this->getOrderKey(), null);
        
        if (Str::startsWith($sortBy, ['+', '-'])) {
            $direction = Str::startsWith($sortBy, '+') ? 'asc' : 'desc';
            $sortBy = Str::substr($sortBy, 1);
        }

        return [$sortBy, $direction];
    }

    /**
     * Apply the sorts to a query using the current request
     */
    public function sortQuery(Builder $builder, Request $request = null): void
    {
        [$sortBy, $direction] = $this->getSortParameters($request);

        // Get the column sorts as well as column
        // $columnSorts = $this->getColumns()->map(fn (BaseColumn $column) => $column->getSort())->filter();

        // $sorts = $this->getSorts()->merge($columnSorts);

        $sorts = $this->getSorts();
        // Need to handle defaults case
        $sorts->each(static fn (Sort $sort) => $sort->apply($builder, $sortBy, $direction));
    }
}
