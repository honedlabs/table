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
    protected $sortAs;

    /**
     * @var string
     */
    protected static $globalSortAs = 'sort';

    /**
     * @var string
     */
    protected $orderAs;

    /**
     * @var string
     */
    protected static $globalOrderAs = 'order';

    /**
     * @var string
     */
    protected $defaultOrder;

    /**
     * @var string
     */
    protected static $globalDefaultOrder = 'asc';

    /**
     * @var bool
     */
    protected $signed;

    /**
     * @var bool
     */
    protected static $globalSigned = true;

    /**
     * Set the list of sorts to apply to the table.
     * 
     * @param array<int, \Honed\Table\Sorts\BaseSort>|null $sorts
     * @return void
     */
    public function setSorts(array|null $sorts)
    {
        if (is_null($sorts)) {
            return;
        }

        $this->sorts = $sorts;
    }

    /**
     * Configure the default query parameter to use for sorting.
     * 
     * @param string $sortAs
     * @return void
     */
    public static function setSortAs(string $sortAs)
    {
        static::$globalSortAs = $sortAs;
    }

    /**
     * Configure the default query parameter to use for ordering.
     * 
     * @param string $orderAs
     * @return void
     */
    public static function setOrderAs(string $orderAs)
    {
        static::$globalOrderAs = $orderAs;
    }

    /**
     * Configure the default order to use for sorting.
     * 
     * @param string $defaultOrder
     * @return void
     */
    public static function setDefaultOrder(string $defaultOrder)
    {
        static::$globalDefaultOrder = $defaultOrder;
    }

    /**
     * Configure whether to enable signed sorting for all tables by default.
     * 
     * @param bool $signed
     * @return void
     */
    public static function enableSigned(bool $signed = true)
    {
        static::$globalSigned = $signed;
    }

    /**
     * Configure whether to disable signed sorting for all tables by default.
     * 
     * @param bool $signed
     * @return void
     */
    public static function disableSigned(bool $signed = false)
    {
        static::$globalSigned = $signed;
    }

    /**
     * Get the query parameter to use for sorting.
     * 
     * @return string
     */
    public function getSortAs()
    {
        return $this->inspect('sortAs', static::$globalSortAs);
    }

    /**
     * Get the query parameter to use for ordering.
     * 
     * @return string
     */
    public function getOrderAs()
    {
        return $this->inspect('orderAs', static::$globalOrderAs);
    }

    /**
     * Get the default order to use for sorting if one is not supplied.
     * 
     * @return string
     */
    public function getDefaultOrder()
    {
        return $this->inspect('defaultOrder', static::$globalDefaultOrder);
    }

    /**
     * Determine whether to enable signed sorting.
     * 
     * @return bool
     */
    public function isSigned()
    {
        return $this->inspect('signed', static::$globalSigned);
    }

    /**
     * Get the sorting field to use from the request query parameters.
     * 
     * @return string|null
     */
    public function getSortTerm()
    {
        $value = request()->input($this->getSortAs());
        
        if (is_null($value)) {
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
        $direction = request()->input($this->getOrderAs());

        if (\is_null($direction) || ! \in_array($direction, ['asc', 'desc'])) {
            return null;
        }

        return $direction;
    }

    /**
     * Get the direction to use from a term if signed sorting is enabled.
     * 
     * @param string $term
     * @return string
     */
    public function getSignedTerm(string $term)
    {
        if (! $this->isSigned()) {
            return $term;
        }

        return $term === 'asc' ? 'desc' : 'asc';
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