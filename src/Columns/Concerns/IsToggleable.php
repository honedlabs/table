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
     */
    public function always(bool $always = true): static
    {
        $this->always = $always;

        return $this;
    }

    /**
     * Set the column to be shown sometimes; it's visibility can be toggled.
     * The boolean provided indicates whether the column is visible
     * to begin with - if false, the column will be shown initially.
     */
    public function sometimes(bool $sometimes = true): static
    {
        $this->sometimes = $sometimes;

        return $this;
    }

    /**
     * Determine if the column is always shown.
     */
    public function isAlways(): bool
    {
        return $this->always;
    }

    /**
     * Determine if the column is hidden on initial load.
     */
    public function isSometimes(): bool
    {
        return $this->sometimes;
    }

    /**
     * Determine if the column can be toggleable.
     */
    public function isToggleable(): bool
    {
        if ($this->isKey() || $this->isAlways()) {
            return false;
        }

        return true;
    }

    /**
     * Determine if this column should be shown on initial load.
     *
     * @param  array<int,string>|null  $params
     */
    public function isDisplayed(?array $params = null): bool
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
