<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Illuminate\Support\Str;

trait HasBreakpoint
{
    const ExtraSmall = 'xs';

    const Small = 'sm';

    const Medium = 'md';

    const Large = 'lg';

    const ExtraLarge = 'xl';

    const BREAKPOINTS = [self::ExtraSmall, self::Small, self::Medium, self::Large, self::ExtraLarge];

    /**
     * @var string|null
     */
    protected $breakpoint = null;

    /**
     * Set the breakpoint, chainable.
     *
     * @throws \InvalidArgumentException
     */
    public function breakpoint(string $breakpoint): static
    {
        $this->setBreakpoint($breakpoint);

        return $this;
    }

    /**
     * Set the breakpoint quietly.
     *
     * @throws \InvalidArgumentException
     */
    public function setBreakpoint(?string $breakpoint): void
    {
        if (\is_null($breakpoint)) {
            return;
        }

        $breakpoint = Str::lower($breakpoint);

        if (! in_array($breakpoint, self::BREAKPOINTS)) {
            throw new \InvalidArgumentException("The provided breakpoint [$breakpoint] is invalid. Please provide one of the following: ".implode(', ', self::BREAKPOINTS));
        }

        $this->breakpoint = $breakpoint;
    }

    /**
     * Get the breakpoint.
     */
    public function getBreakpoint(): ?string
    {
        return $this->breakpoint;
    }

    /**
     * Determine if it has a breakpoint.
     */
    public function hasBreakpoint(): bool
    {
        return ! \is_null($this->breakpoint);
    }

    /**
     * Set the breakpoint to extra small.
     *
     * @throws \InvalidArgumentException
     */
    public function xs(): static
    {
        return $this->breakpoint(self::ExtraSmall);
    }

    /**
     * Set the breakpoint to small.
     *
     * @throws \InvalidArgumentException
     */
    public function sm(): static
    {
        return $this->breakpoint(self::Small);
    }

    /**
     * Set the breakpoint to medium.
     *
     * @throws \InvalidArgumentException
     */
    public function md(): static
    {
        return $this->breakpoint(self::Medium);
    }

    /**
     * Set the breakpoint to large.
     *
     * @throws \InvalidArgumentException
     */
    public function lg(): static
    {
        return $this->breakpoint(self::Large);
    }

    /**
     * Set the breakpoint to extra large.
     *
     * @throws \InvalidArgumentException
     */
    public function xl(): static
    {
        return $this->breakpoint(self::ExtraLarge);
    }
}
