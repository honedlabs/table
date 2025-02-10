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
        if (! \is_null($modifier)) {
            $this->modifier = $modifier;
        }

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
    public function getModifier(): \Closure|null
    {
        return $this->modifier;
    }

    /**
     * Apply the resource modifier to the resource.
     */
    public function modify(): void
    {
        if (\is_null($this->modifier)) {
            return;
        }
        
        \call_user_func($this->modifier, $this->getResource());
    }
}
