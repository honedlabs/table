<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasSuccess
{
    /**
     * @var string|(\Closure(mixed...):string)|null
     */
    protected $success = null;

    /**
     * Set the success message, chainable.
     *
     * @param  string|\Closure(mixed...):string  $success
     * @return $this
     */
    public function success(string|\Closure $success): static
    {
        $this->setSuccess($success);

        return $this;
    }

    /**
     * Set the success message quietly.
     *
     * @param  string|(\Closure(mixed...):string)|null  $success
     */
    public function setSuccess(string|\Closure|null $success): void
    {
        if (is_null($success)) {
            return;
        }
        $this->success = $success;
    }

    /**
     * Get the success message using the given closure dependencies.
     *
     * @param  array<string, mixed>  $named
     * @param  array<string, mixed>  $typed
     */
    public function getSuccess(array $named = [], array $typed = []): ?string
    {
        return $this->evaluate($this->success, $named, $typed);
    }

    /**
     * Resolve the success message using the given closure dependencies.
     *
     * @param  array<string, mixed>  $named
     * @param  array<string, mixed>  $typed
     */
    public function resolveSuccess(array $named = [], array $typed = []): ?string
    {
        $success = $this->getSuccess($named, $typed);
        $this->setSuccess($success);

        return $success;
    }

    /**
     * Determine if the class has a success message.
     */
    public function hasSuccess(): bool
    {
        return ! \is_null($this->success);
    }
}
