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
     * Set the resource modifier.
     *
     * @param  \Closure|null  $modifier
     * @return $this
     */
    public function modifier($modifier)
    {
        $this->modifier = $modifier;

        return $this;
    }

    /**
     * Determine if the instance has a resource modifier.
     *
     * @return bool
     */
    public function hasModifier()
    {
        return ! \is_null($this->modifier);
    }

    /**
     * Get the resource modifier.
     *
     * @return \Closure|null
     */
    public function getModifier()
    {
        return $this->modifier;
    }
}
