<?php

namespace Honed\Table\Sorts\Concerns;

trait HasDirection
{
    public const Ascending = 'asc';

    public const Descending = 'desc';

    /**
     * @var string|(\Closure():string)|null
     */
    protected $direction = null;

    /**
     * @var string
     */
    protected static $defaultDirection = self::Ascending;

    /**
     * Configure the default direction to use when one is not supplied.
     *
     * @param  string|null  $direction
     * @throws \InvalidArgumentException
     */
    public static function useDefaultDirection(string|null $direction = null): void
    {
        if (! \in_array($direction, [self::Ascending, self::Descending])) {
            throw new \InvalidArgumentException('Direction must be either '.self::Ascending.' or '.self::Descending);
        }

        if (is_null($direction)) {
            static::$defaultDirection = self::Ascending;
        } else {
            static::$defaultDirection = $direction;
        }
    }

    /**
     * Set the default direction to ascending.
     */
    public static function useAscending(): void
    {
        static::useDefaultDirection(self::Ascending);
    }

    /**
     * Set the default direction to descending.
     */
    public static function useDescending(): void
    {
        static::useDefaultDirection(self::Descending);
    }

    /**
     * Get the default direction.
     *
     * @return string
     */
    public static function getDefaultDirection(): string
    {
        return static::$defaultDirection;
    }

    /**
     * Set the direction, chainable.
     *
     * @param  string|\Closure():string  $direction
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
     * @param  string|\Closure():string|null  $direction
     */
    public function setDirection(string|\Closure|null $direction): void
    {
        $this->direction = $direction;
    }

    /**
     * Get the direction
     */
    public function getDirection(): ?string
    {
        return $this->evaluate($this->direction);
    }

    /**
     * Determine if the direction is not set
     */
    public function missingDirection(): bool
    {
        return \is_null($this->direction);
    }

    /**
     * Determine if the direction is set
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
