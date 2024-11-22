<?php

declare(strict_types=1);

namespace Honed\Table\Actions\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait DeselectsOnEnd
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $deselectOnEnd = false;

    /**
     * Set the deselectOnEnd property, chainable.
     *
     * @param  bool|(\Closure():bool)  $deselectOnEnd
     * @return $this
     */
    public function deselectOnEnd(bool|\Closure $deselectOnEnd = true): static
    {
        $this->setDeselectOnEnd($deselectOnEnd);

        return $this;
    }

    /**
     * Set the deselectOnEnd property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $deselectOnEnd
     */
    public function setDeselectOnEnd(bool|\Closure|null $deselectOnEnd): void
    {
        if (\is_null($deselectOnEnd)) {
            return;
        }
        $this->deselectOnEnd = $deselectOnEnd;
    }

    /**
     * Determine if the class is deselectOnEnd.
     */
    public function deselectsOnEnd(): bool
    {
        return (bool) $this->evaluate($this->deselectOnEnd);
    }

    /**
     * Determine if the class is not deselectOnEnd.
     */
    public function doesNotDeselectOnEnd(): bool
    {
        return ! $this->deselectsOnEnd();
    }
}
