<?php

declare(strict_types=1);

namespace Honed\Table\Filters\Concerns;

trait OnlyStrictValues
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $onlyStrictValues = false;

    /**
     * Allow only strict values, chainable.
     *
     * @param  bool|(\Closure():bool)  $onlyStrictValues
     * @return $this
     */
    public function onlyStrictValues(bool|\Closure $onlyStrictValues = true): static
    {
        $this->setOnlyStrictValues($onlyStrictValues);

        return $this;
    }

    /**
     * Allow only strict values quietly.
     *
     * @param  bool|(\Closure():bool)|null  $onlyStrictValues
     */
    public function setOnlyStrictValues(bool|\Closure|null $onlyStrictValues): void
    {
        if (\is_null($onlyStrictValues)) {
            return;
        }
        $this->onlyStrictValues = $onlyStrictValues;
    }

    /**
     * Determine if the class allows only strict values.
     */
    public function allowsOnlyStrictValues(): bool
    {
        return (bool) value($this->onlyStrictValues);
    }

    /**
     * Determine if the class allows all values.
     */
    public function allowsAllValues(): bool
    {
        return ! $this->allowsOnlyStrictValues();
    }
}
