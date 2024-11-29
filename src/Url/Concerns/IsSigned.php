<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsSigned
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $signed = false;

    /**
     * Set the url to be signed, chainable.
     *
     * @param  bool|(\Closure():bool)  $signed
     * @return $this
     */
    public function signed(bool|\Closure $signed = true): static
    {
        $this->setSigned($signed);

        return $this;
    }

    /**
     * Set the url to be signed property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $signed
     */
    public function setSigned(bool|\Closure|null $signed): void
    {
        if (\is_null($signed)) {
            return;
        }
        $this->signed = $signed;
    }

    /**
     * Determine if the url should be signed.
     */
    public function isSigned(): bool
    {
        return (bool) $this->evaluate($this->signed);
    }

    /**
     * Determine if the url should not be signed.
     */
    public function isNotSigned(): bool
    {
        return ! $this->isSigned();
    }
}
