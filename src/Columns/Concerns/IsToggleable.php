<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

trait IsToggleable
{
    /**
     * @var bool
     */
    protected $always = false;

    /**
     * @var bool
     */
    protected $sometimes = false;

    /**
     * Set the column to always be shown; it's visibility cannot be toggled.
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
     * Set the column to be shown sometimes; it's visibility can be toggled.
     * The boolean provided indicates whether the column is visible
     * to begin with - if false, the column will be shown initially.
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
     * Determine if the column is always shown.
     *
     * @return bool
     */
    public function isAlways()
    {
        return $this->always;
    }

    /**
     * Determine if the column is hidden on initial load.
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
    public function display($params = null)
    {
        $active = $this->isDisplayed($params);

        $this->active($active);

        return $active;
    }

    /**
     * Determine if this column should be shown on initial load.
     *
     * @param  array<int,string>|null  $params
     * @return bool
     */
    public function isDisplayed($params = null)
    {
        if (! $this->isToggleable()) {
            return true;
        }

        if (\is_null($params)) {
            return ! $this->isSometimes();
        }

        return \in_array($this->getName(), $params);
    }
}
