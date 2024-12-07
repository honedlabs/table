<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait Sortable
{
    /**
     * @var array<int,\Honed\Table\Sorts\BaseSort>
     */
    protected $sorts;

    /**
     * @var string
     */
    protected $sortName;

    /**
     * @var string
     */
    protected static $useSortName = 'sort';

    /**
     * @var string
     */
    protected $orderName;

    /**
     * @var string
     */
    protected static $useOrderName = 'order';

    /**
     * @var string
     */
    protected $defaultOrder;

    /**
     * @var string
     */
    protected static $useDefaultOrder = 'asc';

    /**
     * Set the list of sorts to apply to the table.
     *
     * @param  array<int, \Honed\Table\Sorts\BaseSort>|null  $sorts
     * @return void
     */
    public function setSorts(?array $sorts)
    {
        if (is_null($sorts)) {
            return;
        }

        $this->sorts = $sorts;
    }

    /**
     * Configure the default query parameter to use for sorting.
     *
     * @return void
     */
    public static function useSortName(string $sortName)
    {
        static::$useSortName = $sortName;
    }

    /**
     * Configure the default query parameter to use for ordering.
     *
     * @return void
     */
    public static function useOrderName(string $orderName)
    {
        static::$useOrderName = $orderName;
    }

    /**
     * Configure the default order to use for sorting.
     *
     * @return void
     */
    public static function useDefaultOrder(string $defaultOrder)
    {
        static::$useDefaultOrder = $defaultOrder;
    }

    /**
     * Get the query parameter to use for sorting.
     *
     * @return string
     */
    public function getSortName()
    {
        return $this->inspect('sortName', static::$useSortName);
    }

    /**
     * Get the query parameter to use for ordering.
     *
     * @return string
     */
    public function getOrderName()
    {
        return $this->inspect('orderName', static::$useOrderName);
    }

    /**
     * Get the default order to use for sorting if one is not supplied.
     *
     * @return string
     */
    public function getDefaultOrder()
    {
        return $this->inspect('defaultOrder', static::$useDefaultOrder);
    }

    /**
     * Get the sorting field to use from the request query parameters.
     *
     * @return string|null
     */
    public function getSortTerm()
    {
        $value = request()->input($this->getSortName());

        if (\is_null($value)) {
            return null;
        }

        return (string) $value;
    }

    /**
     * Get the sorting direction to use from the request query parameters.
     *
     * @return string|null
     */
    public function getOrderTerm()
    {
        $direction = request()->input($this->getOrderName());

        if (\is_null($direction) || ! \in_array($direction, ['asc', 'desc'])) {
            return null;
        }

        return $direction;
    }

    /**
     * Get the sorts to apply to the resource.
     *
     * @return array<int, \Honed\Table\Sorts\BaseSort>
     */
    public function getSorts()
    {
        return $this->inspect('sorts', []);
    }

    /**
     * Get the sort name to use, and direction for the current request.
     *
     * @return array{string,string}
     */
    public function getSortBy()
    {
        // Check if signed, and remove the +- terms from it -> TODO
        return [$this->getSortTerm(), $this->getOrderTerm()];
    }
}
