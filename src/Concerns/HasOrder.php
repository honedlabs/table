<?php

namespace Conquest\Table\Concerns;

trait HasOrder
{
    /**
     * @var string
     */
    protected $order;

    const Ascending = 'asc';

    const Descending = 'desc';

    /**
     * Set the key to use as query parameter for ordering.
     *
     * @param  string|null  $order
     */
    protected function setOrder($order): void
    {
        if (is_null($order)) {
            return;
        }

        $this->order = $order;
    }

    /**
     * Get the order key to use.
     *
     * @internal
     *
     * @return string
     */
    protected function definedOrder()
    {
        if (isset($this->order)) {
            return $this->order;
        }

        if (method_exists($this, 'order')) {
            return $this->order();
        }

        return config('table.sorting.order', 'order');
    }

    /**
     * Get the order direction to use.
     *
     * @return string|null
     */
    public function getOrder(): string
    {
        return $this->sanitiseOrder($this->definedOrder());
    }

    /**
     * Get the order direction from the request query parameters.
     *
     * @internal
     *
     * @return string|null
     */
    protected function getOrderFromRequest()
    {
        return request()->input($this->getOrderKey(), null);
    }

    /**
     * Restrict potential directions to be asc or desc.
     *
     * @param  string|null  $value
     * @return string
     */
    public function sanitiseOrder($value)
    {
        return in_array(
            $value,
            [self::Ascending, self::Descending],
            true
        ) ? $value :
            config('table.sorting.default_order', self::Ascending);
    }
}
