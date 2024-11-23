<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsNamed
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $named = false;

    /**
     * Set the url to be named, chainable.
     *
     * @param  bool|(\Closure():bool)  $named
     * @return $this
     */
    public function named(bool|\Closure $named): static
    {
        $this->setNamed($named);

        return $this;
    }

    /**
     * Set the url to be named property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $named
     */
    public function setNamed(bool|\Closure|null $named): void
    {
        if (\is_null($named)) {
            return;
        }
        $this->named = $named;
    }

    /**
     * Determine if the url should be named.
     */
    public function isNamed(): bool
    {
        return (bool) $this->evaluate($this->named);
    }

    /**
     * Determine if the url should not be named.
     */
    public function isNotNamed(): bool
    {
        return ! $this->isNamed();
    }
}
