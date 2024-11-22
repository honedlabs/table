<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasTooltip
{
    /**
     * @var string|(\Closure():string)|null
     */
    protected $tooltip = null;

    /**
     * Set the tooltip, chainable.
     *
     * @param  string|\Closure():string  $tooltip
     * @return $this
     */
    public function tooltip(string|\Closure $tooltip): static
    {
        $this->setTooltip($tooltip);

        return $this;
    }

    /**
     * Set the tooltip quietly.
     *
     * @param  string|(\Closure():string)|null  $tooltip
     */
    public function setTooltip(string|\Closure|null $tooltip): void
    {
        if (is_null($tooltip)) {
            return;
        }
        $this->tooltip = $tooltip;
    }

    /**
     * Get the tooltip.
     */
    public function getTooltip(): ?string
    {
        return $this->evaluate($this->tooltip);
    }

    /**
     * Determine if the column does not have a tooltip.
     */
    public function missingTooltip(): bool
    {
        return \is_null($this->tooltip);
    }

    /**
     * Determine if the column has a tooltip.
     */
    public function hasTooltip(): bool
    {
        return ! $this->missingTooltip();
    }
}
