<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsSrOnly
{
    /**
     * @var bool
     */
    protected $srOnly = false;

    /**
     * Set as screen reader only, chainable.
     *
     * @return $this
     */
    public function srOnly(bool $srOnly = true): static
    {
        $this->setSrOnly($srOnly);

        return $this;
    }

    /**
     * Set as screen reader only quietly.
     */
    public function setSrOnly(bool $srOnly): void
    {
        $this->srOnly = $srOnly;
    }

    /**
     * Determine if it is only for screen readers.
     */
    public function isSrOnly(): bool
    {
        return $this->srOnly;
    }
}
