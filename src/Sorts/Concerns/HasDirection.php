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
     * @var string|null
     */
    protected $activeDirection = null;

    /**
     * @var string
     */
    protected static $defaultDirection = self::Ascending;

    /**
     * Configure the default direction to use when one is not supplied.
     *
     * @throws \InvalidArgumentException
     */
    public static function useDefaultDirection(?string $direction = null): void
    {
        if (! \in_array($direction, [self::Ascending, self::Descending, null])) {
            throw new \InvalidArgumentException('Direction must be either asc, desc, or null');
        }

        if (\is_null($direction)) {
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
     */
    public static function getDefaultDirection(): string
    {
        return static::$defaultDirection;
    }

    /**
     * Set the direction, chainable.
     *
     * @param  string|(\Closure():string|null)|null  $direction
     * @return $this
     */
    public function direction(string|\Closure|null $direction): static
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
        if (\is_string($direction) && ! \in_array($direction, [self::Ascending, self::Descending])) {
            throw new \InvalidArgumentException('Direction must be either asc, desc, or null');
        }

        $this->direction = $direction;
    }

    /**
     * Get the direction
     */
    public function getDirection(): ?string
    {
        return value($this->direction);
    }

    /**
     * Determine if the direction is set
     */
    public function hasDirection(): bool
    {
        return ! \is_null($this->direction);
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

    /**
     * Set the active direction
     *
     * @internal
     *
     * @param  'asc'|'desc'|null  $direction
     */
    public function setActiveDirection(?string $direction): void
    {
        if (! \in_array($direction, ['asc', 'desc', null])) {
            throw new \InvalidArgumentException('Direction must be either asc, desc, or null');
        }

        $this->activeDirection = $direction;
    }

    /**
     * Get the active direction
     *
     * @internal
     *
     * @return 'asc'|'desc'|null
     */
    public function getActiveDirection(): ?string
    {
        return $this->activeDirection;
    }
}
