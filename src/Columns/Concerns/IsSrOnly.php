<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsSrOnly
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $srOnly = false;

    /**
     * Set the screen reader only property, chainable.
     *
     * @param  bool|(\Closure():bool)  $srOnly
     * @return $this
     */
    public function srOnly(bool|\Closure $srOnly = true): static
    {
        $this->setSrOnly($srOnly);

        return $this;
    }

    /**
     * Set the screen reader only property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $srOnly
     */
    public function setSrOnly(bool|\Closure|null $srOnly): void
    {
        if (\is_null($srOnly)) {
            return;
        }
        $this->srOnly = $srOnly;
    }

    /**
     * Determine if the column is only for screen readers.
     */
    public function isSrOnly(): bool
    {
        return (bool) $this->evaluate($this->srOnly);
    }

    /**
     * Determine if the column is not only for screen readers.
     */
    public function isNotSrOnly(): bool
    {
        return ! $this->isSrOnly();
    }
}
