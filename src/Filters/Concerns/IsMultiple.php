<?php

declare(strict_types=1);

namespace Honed\Table\Filters\Concerns;

trait IsMultiple
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $multiple = false;

    /**
     * Set the multiple property, chainable.
     *
     * @param  bool|(\Closure():bool)  $multiple
     * @return $this
     */
    public function multiple(bool|\Closure $multiple = true): static
    {
        $this->setMultiple($multiple);

        return $this;
    }

    /**
     * Set the multiple property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $multiple
     */
    public function setMultiple(bool|\Closure|null $multiple): void
    {
        if (\is_null($multiple)) {
            return;
        }
        $this->multiple = $multiple;
    }

    /**
     * Determine if the class is multiple.
     */
    public function isMultiple(): bool
    {
        return (bool) value($this->multiple);
    }

    /**
     * Determine if the class is not multiple.
     */
    public function isSingle(): bool
    {
        return ! $this->isMultiple();
    }
}
