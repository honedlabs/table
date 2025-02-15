<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasModifier
{
    /**
     * @var \Closure|null
     */
    protected $modifier;

    /**
     * @return $this
     */
    public function modifier(?\Closure $modifier): static
    {
        $this->modifier = $modifier;

        return $this;
    }

    /**
     * Determine if the instance has a resource modifier.
     */
    public function hasModifier(): bool
    {
        return ! \is_null($this->modifier);
    }

    /**
     * Get the resource modifier.
     */
    public function getModifier(): ?\Closure
    {
        return $this->modifier;
    }
}
