<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasDuration
{
    /**
     * @var int|\Carbon\Carbon|\Closure|null
     */
    protected $duration = 0;

    /**
     * Set the duration, chainable.
     *
     * @param  int|\Carbon\Carbon|\Closure|null $duration
     * @return $this
     */
    public function duration(int|\Carbon\Carbon|\Closure|null $duration): static
    {
        $this->setDuration($duration);

        return $this;
    }

    /**
     * Set the duration quietly.
     *
     * @param  int|\Carbon\Carbon|\Closure|null $duration
     */
    public function setDuration(int|\Carbon\Carbon|\Closure|null $duration): void
    {
        if (is_null($duration)) {
            return;
        }
        $this->duration = $duration;
    }

    /**
     * Get the duration.
     */
    public function getDuration(): int|\Carbon\Carbon
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
