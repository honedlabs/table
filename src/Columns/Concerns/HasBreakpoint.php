<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Illuminate\Support\Str;
use InvalidArgumentException;

trait HasBreakpoint
{
    protected ?string $breakpoint = null;

    public const ExtraSmall = 'xs';

    public const Small = 'sm';

    public const Medium = 'md';

    public const Large = 'lg';

    public const ExtraLarge = 'xl';

    public const BREAKPOINTS = [
        self::ExtraSmall,
        self::Small,
        self::Medium,
        self::Large,
        self::ExtraLarge,
    ];

    /**
     * @throws InvalidArgumentException
     */
    public function breakpoint(string $breakpoint): static
    {
        $this->setBreakpoint($breakpoint);

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setBreakpoint(?string $breakpoint): void
    {
        if (is_null($breakpoint)) {
            return;
        }
        $breakpoint = Str::lower($breakpoint);

        if (! in_array($breakpoint, self::BREAKPOINTS)) {
            throw new InvalidArgumentException("The provided breakpoint [$breakpoint] is invalid. Please provide one of the following: ".implode(', ', self::BREAKPOINTS));
        }
        $this->breakpoint = $breakpoint;
    }

    public function getBreakpoint(): ?string
    {
        return $this->evaluate($this->breakpoint);
    }

    public function hasBreakpoint(): bool
    {
        return ! $this->missingBreakpoint();
    }

    public function missingBreakpoint(): bool
    {
        return is_null($this->breakpoint);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function xs(): static
    {
        return $this->breakpoint(self::ExtraSmall);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function sm(): static
    {
        return $this->breakpoint(self::Small);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function md(): static
    {
        return $this->breakpoint(self::Medium);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function lg(): static
    {
        return $this->breakpoint(self::Large);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function xl(): static
    {
        return $this->breakpoint(self::ExtraLarge);
    }
}
