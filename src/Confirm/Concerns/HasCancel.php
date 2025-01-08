<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasCancel
{
    /**
     * @var string|(\Closure(mixed...):string)|null
     */
    protected $cancel = null;

    /**
     * Set the cancel, chainable.
     *
     * @param  string|\Closure(mixed...):string  $cancel
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
     * @param  string|(\Closure(mixed...):string)|null  $cancel
     */
    public function setCancel(string|\Closure|null $cancel): void
    {
        if (\is_null($cancel)) {
            return;
        }

        $this->cancel = $cancel;
    }

    /**
     * Get the cancel message using the given closure dependencies.
     *
     * @param  array<string, mixed>  $named
     * @param  array<string, mixed>  $typed
     */
    public function getCancel(array $named = [], array $typed = []): ?string
    {
        return $this->evaluate($this->cancel, $named, $typed);
    }

    /**
     * Resolve the cancel message using the given closure dependencies.
     *
     * @param  array<string, mixed>  $named
     * @param  array<string, mixed>  $typed
     */
    public function resolveCancel(array $named = [], array $typed = []): ?string
    {
        $cancel = $this->getCancel($named, $typed);
        $this->setCancel($cancel);

        return $cancel;
    }

    /**
     * Determine if the class has a cancel.
     */
    public function hasCancel(): bool
    {
        return ! \is_null($this->cancel);
    }
}
