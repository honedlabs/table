<?php

declare(strict_types=1);

namespace Honed\Table\Filters\Concerns;

trait OnlyStrictValues
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $strict = false;

    /**
     * Allow only strict values, chainable.
     *
     * @param  bool|(\Closure():bool)  $strict
     * @return $this
     */
    public function strict(bool|\Closure $strict = true): static
    {
        $this->setStrict($strict);

        return $this;
    }

    /**
     * Allow only strict values quietly.
     *
     * @param  bool|(\Closure():bool)|null  $strict
     */
    public function setStrict(bool|\Closure|null $strict): void
    {
        if (\is_null($strict)) {
            return;
        }
        $this->strict = $strict;
    }

    /**
     * Determine if the class allows only strict values.
     */
    public function onlyStrictValues(): bool
    {
        return (bool) value($this->strict);
    }

    /**
     * Determine if the class allows all values.
     */
    public function allowsAllValues(): bool
    {
        return ! $this->onlyStrictValues();
    }
}
