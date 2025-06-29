<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Contracts\IsOrderable;

trait Orderable
{
    /**
     * Whether the columns are orderable.
     *
     * @var bool
     */
    protected $orderable = false;

    /**
     * Set the instance to be orderable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function orderable($value = true)
    {
        $this->orderable = $value;

        return $this;
    }

    /**
     * Set the instance to not be orderable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notOrderable($value = true)
    {
        return $this->orderable(! $value);
    }

    /**
     * Determine if the instance is orderable.
     *
     * @return bool
     */
    public function isOrderable()
    {
        return $this->orderable || $this instanceof IsOrderable;
    }

    /**
     * Determine if the instance is not orderable.
     *
     * @return bool
     */
    public function isNotOrderable()
    {
        return ! $this->isOrderable();
    }
}
