<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Contracts\IsSelectable;

trait Selectable
{
    /**
     * The columns to select, indicative of whether the instance is selectable.
     *
     * @var bool|string|\Illuminate\Contracts\Database\Query\Expression|array<int, string|\Illuminate\Contracts\Database\Query\Expression>
     */
    protected $selectable = false;

    /**
     * Set the instance to be selectable, optionally with a list of columns to select.
     *
     * @param  bool|\Illuminate\Contracts\Database\Query\Expression|array<int, string|\Illuminate\Contracts\Database\Query\Expression>  $value
     * @return $this
     */
    public function selectable($value = true): static
    {
        $this->selectable = $value;

        return $this;
    }

    /**
     * Set the instance to not be selectable.
     *
     * @return $this
     */
    public function notSelectable(bool $value = true): static
    {
        return $this->selectable(! $value);
    }

    /**
     * Select the columns to be displayed.
     *
     * @param  string|\Illuminate\Contracts\Database\Query\Expression|array<int, string|\Illuminate\Contracts\Database\Query\Expression>  $selects
     * @return $this
     */
    public function select($selects): static
    {
        /** @var array<int, string|\Illuminate\Contracts\Database\Query\Expression> */
        $selects = is_array($selects) ? $selects : func_get_args();

        $this->selectable = array_merge($this->getSelects(), $selects);

        return $this;
    }

    /**
     * Determine if the instance is selectable.
     */
    public function isSelectable(): bool
    {
        return (bool) $this->selectable || $this instanceof IsSelectable;
    }

    /**
     * Determine if the instance is not selectable.
     */
    public function isNotSelectable(): bool
    {
        return ! $this->isSelectable();
    }

    /**
     * Get the columns to select.
     *
     * @return array<int, string>
     */
    public function getSelects(): array
    {
        /** @var array<int, string|\Illuminate\Contracts\Database\Query\Expression> */
        return match (true) {
            is_array($this->selectable) => $this->selectable,
            is_string($this->selectable) => [$this->selectable],
            default => []
        };
    }
}
