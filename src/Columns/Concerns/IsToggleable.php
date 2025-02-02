<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsToggleable
{
    /**
     * @var bool
     */
    protected $toggleable = false;

    /**
     * Set as toggleable, chainable.
     *
     * @return $this
     */
    public function toggleable(bool $toggleable = true): static
    {
        $this->toggleable = $toggleable;

        return $this;
    }

    /**
     * Determine if it is toggleable.
     */
    public function isToggleable(): bool
    {
        return $this->toggleable;
    }
}
