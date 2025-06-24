<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Contracts\IsSelectable;

trait Selectable
{
    /**
     * The columns to select, indicative of whether the instance is selectable.
     *
     * @var bool|string|array<int, string>
     */
    protected $selectable = false;

    /**
     * Set the instance to be selectable, optionally with a list of columns to select.
     *
     * @param  bool|array<int, string>  $value
     * @return $this
     */
    public function selectable($value = true)
    {
        $this->selectable = $value;

        return $this;
    }

    /**
     * Select the columns to be displayed.
     *
     * @param  string|array<int, string>  $selects
     * @return $this
     */
    public function select($selects)
    {
        /** @var array<int, string> */
        $selects = is_array($selects) ? $selects : func_get_args();

        $this->selectable = array_merge($this->getSelects(), $selects);

        return $this;
    }

    /**
     * Determine if the instance is selectable.
     *
     * @return bool
     */
    public function isSelectable()
    {
        return ((bool) $this->selectable) || $this instanceof IsSelectable;
    }

    /**
     * Get the columns to select.
     *
     * @return array<int, string>
     */
    public function getSelects()
    {
        /** @var array<int, string> */
        return match (true) {
            is_array($this->selectable) => $this->selectable,
            is_string($this->selectable) => [$this->selectable],
            default => []
        };
    }
}
