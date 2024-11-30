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
     * @param  string|\Closure(mixed...):string  $tooltip
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
     * @param  string|(\Closure(mixed...):string)|null  $tooltip
     */
    public function setTooltip(string|\Closure|null $tooltip): void
    {
        if (\is_null($tooltip)) {
            return;
        }
        $this->tooltip = $tooltip;
    }

    /**
     * Get the tooltip using the given closure dependencies.
     *
     * @param  array<string, mixed>  $named
     * @param  array<string, mixed>  $typed
     */
    public function getTooltip(array $named = [], array $typed = []): ?string
    {
        return $this->evaluate($this->tooltip, $named, $typed);
    }

    /**
     * Resolve the tooltip using the given closure dependencies.
     *
     * @param  array<string, mixed>  $named
     * @param  array<string, mixed>  $typed
     */
    public function resolveTooltip(array $named = [], array $typed = []): ?string
    {
        $this->setTooltip($this->getTooltip($named, $typed));

        return $this->tooltip;
    }

    /**
     * Determine if the column does not have a tooltip.
     *
     * @return bool
     */
    public function missingTooltip(): bool
    {
        return \is_null($this->tooltip);
    }

    /**
     * Determine if the column has a tooltip.
     *
     * @return bool
     */
    public function hasTooltip(): bool
    {
        return ! $this->missingTooltip();
    }
}
