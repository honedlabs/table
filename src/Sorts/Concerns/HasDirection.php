<?php

namespace Honed\Table\Sorts\Concerns;

trait HasDirection
{
    /**
     * @var string|(\Closure():string)|null
     */
    protected $direction = null;

    public const Ascending = 'asc';
    public const Descending = 'desc';

    /**
     * Set the direction, chainable.
     * 
     * @param string|\Closure():string $direction
     * @return $this
     */
    public function direction(string|\Closure $direction): static
    {
        $this->setDirection($direction);

        return $this;
    }

    /**
     * Set the direction quietly.
     * 
     * @param string|\Closure():string|null $direction
     */
    public function setDirection(string|\Closure|null $direction): void
    {
        $this->direction = $direction;
    }

    /**
     * Get the direction
     * 
     * @return string|null
     */
    public function getDirection(): ?string
    {
        return $this->evaluate($this->direction);
    }

    /**
     * Determine if the direction is not set
     * 
     * @return bool
     */
    public function missingDirection(): bool
    {
        return \is_null($this->direction);
    }

    /**
     * Determine if the direction is set
     * 
     * @return bool
     */
    public function hasDirection(): bool
    {
        return ! $this->missingDirection();
    }

    /**
     * Set the direction to be descending
     * 
     * @return $this
     */
    public function desc(): static
    {
        return $this->direction(self::Descending);
    }

    /**
     * Set the direction to be ascending
     * 
     * @return $this
     */
    public function asc(): static
    {
        return $this->direction(self::Ascending);
    }
}
