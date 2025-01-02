<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasSuccess
{
    /**
     * @var string|(\Closure():string)|null
     */
    protected $success = null;

    /**
     * Set the success message, chainable.
     *
     * @param  string|\Closure():string  $success
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
     * @param  string|(\Closure():string)|null  $success
     */
    public function setSuccess(string|\Closure|null $success): void
    {
        if (is_null($success)) {
            return;
        }
        $this->success = $success;
    }

    /**
     * Get the success message.
     */
    public function getSuccess(): ?string
    {
        return $this->evaluate($this->success);
    }

    /**
     * Determine if the class has a success message.
     */
    public function hasSuccess(): bool
    {
        return ! \is_null($this->success);
    }
}
