<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasResource
{
    /**
     * @var class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|null
     */
    protected $resource;

    /**
     * @var \Closure|null
     */
    protected $modifier;

    /**
     * @return \Illuminate\Database\Eloquent\Model|class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function getResource(): Model|string|Builder
    {
        return match (true) {
            \method_exists($this, 'resource') => $this->resource(),
            \property_exists($this, 'resource') && ! \is_null($this->resource) => $this->resource,
            default => $this->guessResource()
        };
    }

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    public function guessResource(): string
    {
        return str(static::class)
            ->classBasename()
            ->beforeLast('Table')
            ->singular()
            ->prepend('\\App\\Models\\')
            ->value();
    }

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
    public function getModifier(): ?\Closure
    {
        return $this->modifier;
    }

    /**
     * Apply the resource modifier to the resource.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $resource
     */
    public function modifyResource(Builder $resource): void
    {
        if (! $this->hasModifier()) {
            return;
        }

        \call_user_func($this->modifier, $resource);
    }
}
