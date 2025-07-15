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
     * @return $this
     */
    public function orderable(bool $value = true): static
    {
        $this->orderable = $value;

        return $this;
    }

    /**
     * Set the instance to not be orderable.
     *
     * @return $this
     */
    public function notOrderable(bool $value = true): static
    {
        return $this->orderable(! $value);
    }

    /**
     * Determine if the instance is orderable.
     */
    public function isOrderable(): bool
    {
        return $this->orderable || $this instanceof IsOrderable;
    }

    /**
     * Determine if the instance is not orderable.
     */
    public function isNotOrderable(): bool
    {
        return ! $this->isOrderable();
    }
}
