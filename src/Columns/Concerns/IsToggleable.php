<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsToggleable
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $toggleable = false;

    /**
     * Set the toggleable property, chainable.
     *
     * @param  bool|(\Closure():bool)  $toggleable
     * @return $this
     */
    public function toggleable(bool|\Closure $toggleable = true): static
    {
        $this->setToggleable($toggleable);

        return $this;
    }

    /**
     * Set the toggleable property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $toggleable
     */
    public function setToggleable(bool|\Closure|null $toggleable): void
    {
        if (\is_null($toggleable)) {
            return;
        }
        $this->toggleable = $toggleable;
    }

    /**
     * Determine if the column is toggleable.
     */
    public function isToggleable(): bool
    {
        return (bool) $this->evaluate($this->toggleable);
    }

    /**
     * Determine if the column is not toggleable.
     */
    public function isNotToggleable(): bool
    {
        return ! $this->isToggleable();
    }
}
