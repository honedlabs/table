<?php

declare(strict_types=1);

namespace Honed\Table\Actions\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsInline
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $inline = false;

    /**
     * Set the inline property, chainable.
     *
     * @param  bool|(\Closure():bool)  $inline
     * @return $this
     */
    public function inline(bool|\Closure $inline = true): static
    {
        $this->setInline($inline);

        return $this;
    }

    /**
     * Set the inline property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $inline
     */
    public function setInline(bool|\Closure|null $inline): void
    {
        if (\is_null($inline)) {
            return;
        }
        $this->inline = $inline;
    }

    /**
     * Determine if the class is inline.
     */
    public function isInline(): bool
    {
        return (bool) $this->evaluate($this->inline);
    }

    /**
     * Determine if the class is not inline.
     */
    public function isNotInline(): bool
    {
        return ! $this->isInline();
    }
}
