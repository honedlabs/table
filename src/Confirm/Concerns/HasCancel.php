<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasCancel
{
    /**
     * @var string|(\Closure():string)|null
     */
    protected $cancel = null;

    /**
     * Set the cancel, chainable.
     *
     * @param  string|\Closure():string  $cancel
     * @return $this
     */
    public function cancel(string|\Closure $cancel): static
    {
        $this->setCancel($cancel);

        return $this;
    }

    /**
     * Set the cancel quietly.
     *
     * @param  string|(\Closure():string)|null  $cancel
     */
    public function setCancel(string|\Closure|null $cancel): void
    {
        if (is_null($cancel)) {
            return;
        }
        $this->cancel = $cancel;
    }

    /**
     * Get the cancel.
     */
    public function getCancel(): ?string
    {
        return $this->evaluate($this->cancel);
    }

    /**
     * Determine if the class has a cancel.
     */
    public function hasCancel(): bool
    {
        return ! \is_null($this->cancel);
    }
}
