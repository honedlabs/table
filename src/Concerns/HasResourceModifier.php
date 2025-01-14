<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasResourceModifier
{
    /**
     * @var \Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder|null)
     */
    protected $modifier;

    /**
     * Set the resource modifier quietly.
     *
     * @param  (\Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder|null))|null  $modifier
     */
    public function setModifier(?\Closure $modifier): void
    {
        if (\is_null($modifier)) {
            return;
        }

        $this->modifier = $modifier;
    }

    /**
     * Determine if there is a resource modifier.
     */
    public function hasResourceModifier(): bool
    {
        return (\property_exists($this, 'modifier') && \is_callable($this->modifier))
            || \method_exists($this, 'modifier');
    }

    /**
     * Apply the resource modifier to the builder.
     */
    public function modifyResource(Builder $builder): void
    {
        match (true) {
            \property_exists($this, 'modifier') && \is_callable($this->modifier) => \call_user_func($this->modifier, $builder),
            \method_exists($this, 'modifier') => \call_user_func([$this, 'modifier'], $builder),
            default => null,
        };
    }
}
