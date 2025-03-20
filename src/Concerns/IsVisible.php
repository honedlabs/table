<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait IsVisible
{
    /**
     * Set the instance to always be visible.
     *
     * @var bool
     */
    protected $always = false;

    /**
     * Set the instance to sometimes be visible.
     *
     * @var bool
     */
    protected $sometimes = false;

    /**
     * Set the column to always be visible.
     *
     * @param  bool  $always
     * @return $this
     */
    public function always($always = true)
    {
        $this->always = $always;

        return $this;
    }

    /**
     * Determine if the column is always visible.
     *
     * @return bool
     */
    public function isAlways()
    {
        return $this->always;
    }

    /**
     * Set the column to sometimes be visible.
     *
     * @param  bool  $sometimes
     * @return $this
     */
    public function sometimes($sometimes = true)
    {
        $this->sometimes = $sometimes;

        return $this;
    }

    /**
     * Determine if the column is sometimes visible.
     *
     * @return bool
     */
    public function isSometimes()
    {
        return $this->sometimes;
    }

    /**
     * Determine if the column can be toggleable.
     *
     * @return bool
     */
    public function isToggleable()
    {
        return ! ($this->isKey() || $this->isAlways());
    }

    /**
     * Activate the displayed columns, and return the active columns.
     *
     * @param  array<int,string>|null  $params
     * @return bool
     */
    public function visible($params = null)
    {
        $active = $this->isVisible($params);

        $this->active($active);

        return $active;
    }

    /**
     * Determine if this column should be shown on initial load.
     *
     * @param  array<int,string>|null  $params
     * @return bool
     */
    public function isVisible($params = null)
    {
        if (! $this->isToggleable()) {
            return true;
        }

        if (\is_null($params)) {
            return ! $this->isSometimes();
        }

        return \in_array($this->getParameter(), $params);
    }
}
