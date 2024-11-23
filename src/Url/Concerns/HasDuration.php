<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasDuration
{
    /**
     * @var int|(\Closure():int)
     */
    protected $duration = 0;

    /**
     * Set the duration, chainable.
     *
     * @param  int|\Closure():int  $duration
     * @return $this
     */
    public function duration(int|\Closure $duration): static
    {
        $this->setDuration($duration);

        return $this;
    }

    /**
     * Set the duration quietly.
     *
     * @param  int|(\Closure():int)|null  $duration
     */
    public function setDuration(int|\Closure|null $duration): void
    {
        if (is_null($duration)) {
            return;
        }
        $this->duration = $duration;
    }

    /**
     * Get the duration.
     */
    public function getDuration(): int
    {
        return $this->evaluate($this->duration);
    }

    /**
     * Determine if the class has a duration.
     */
    public function isTemporary(): bool
    {
        return (bool) $this->getDuration() > 0;
    }

    /**
     * Determine if the class does not have a duration.
     */
    public function isNotTemporary(): bool
    {
        return ! $this->isTemporary();
    }

}
