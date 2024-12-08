<?php

declare(strict_types=1);

namespace Honed\Table\Sorts\Concerns;

trait IsBound
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $bound = true;

    /**
     * Set the url to be bound, chainable.
     *
     * @param  bool|(\Closure():bool)  $bound
     * @return $this
     */
    public function bound(bool|\Closure $bound = true): static
    {
        $this->setBound($bound);

        return $this;
    }

    /**
     * Set the url to be bound property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $bound
     */
    public function setBound(bool|\Closure|null $bound): void
    {
        if (\is_null($bound)) {
            return;
        }
        $this->bound = $bound;
    }

    /**
     * Determine if the url should be bound.
     *
     * @return bool
     */
    public function isBound(): bool
    {
        return value($this->bound);
    }

    /**
     * Determine if the url should not be bound.
     *
     * @return bool
     */
    public function isNotBound(): bool
    {
        return ! $this->isBound();
    }

    /**
     * Alias for `bound`.
     *
     * @param  bool|(\Closure():bool)  $bound
     * @return $this
     */
    public function free(bool|\Closure $bound = true): static
    {
        return $this->bound($bound);
    }

    /**
     * Alias for `bound` with inverted value.
     *
     * @param  bool|(\Closure():bool)  $unbound
     * @return $this
     */
    public function unbound(bool|\Closure $unbound = true): static
    {
        return $this->bound(! $unbound);
    }
}
