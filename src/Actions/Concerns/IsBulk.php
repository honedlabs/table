<?php

declare(strict_types=1);

namespace Honed\Table\Actions\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsBulk
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $bulk = false;

    /**
     * Set the bulk property, chainable.
     *
     * @param  bool|(\Closure():bool)  $bulk
     * @return $this
     */
    public function bulk(bool|\Closure $bulk = true): static
    {
        $this->setBulk($bulk);

        return $this;
    }

    /**
     * Set the bulk property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $bulk
     */
    public function setBulk(bool|\Closure|null $bulk): void
    {
        if (\is_null($bulk)) {
            return;
        }
        $this->bulk = $bulk;
    }

    /**
     * Determine if the class is bulk.
     */
    public function isBulk(): bool
    {
        return (bool) $this->evaluate($this->bulk);
    }

    /**
     * Determine if the class is not bulk.
     */
    public function isNotBulk(): bool
    {
        return ! $this->isBulk();
    }
}
