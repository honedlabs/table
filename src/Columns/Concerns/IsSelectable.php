<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsSelectable
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $selectable = false;

    /**
     * Set the selectable for bulk actions property, chainable.
     *
     * @param  bool|(\Closure():bool)  $selectable
     * @return $this
     */
    public function selectable(bool|\Closure $selectable = true): static
    {
        $this->setSelectable($selectable);

        return $this;
    }

    /**
     * Set the selectable for bulk actions property quietly.
     *
     * @param  bool|(\Closure():bool)|null $selectable
     */
    public function setSelectable(bool|\Closure|null $selectable): void
    {
        if (\is_null($selectable)) {
            return;
        }
        $this->selectable = $selectable;
    }

    /**
     * Determine if the row is selectable for bulk actions.
     */
    public function isSelectable(): bool
    {
        return (bool) $this->evaluate($this->selectable);
    }

    /**
     * Determine if the row is not selectable for bulk actions.
     */
    public function isNotSelectable(): bool
    {
        return ! $this->isSelectable();
    }
}
