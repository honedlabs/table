<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Exceptions\MissingResourceException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Stringable;

trait HasResource
{
    /**
     * The model class-string, or Eloquent Builder instance to use for the table.
     *
     * @var \Illuminate\Contracts\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>|string
     */
    protected $resource;

    /**
     * The resource query builder instance.
     *
     * @var \Illuminate\Contracts\Database\Eloquent\Builder|null
     */
    protected $builder;

    /**
     * Get the resource to use for the table as an Eloquent query builder.
     *
     * @throws \Honed\Table\Exceptions\MissingResourceException
     */
    public function getResource(): Builder
    {
        return $this->builder ??= $this->resolveResource(match (true) {
            \method_exists($this, 'resource') => $this->resource(),
            \property_exists($this, 'resource') && ! \is_null($this->resource) => $this->resource,
            default => $this->guessResource()
        });
    }

    /**
     * Resolve the given resource into a query builder.
     *
     * @throws \Honed\Table\Exceptions\MissingResourceException
     */
    protected function resolveResource(mixed $resource): Builder
    {
        return match (true) {
            $resource instanceof Builder => $resource,
            $resource instanceof Model => $resource->newQuery(),
            \is_string($resource) => $resource::query(),
            default => throw new MissingResourceException(static::class)
        };
    }

    /**
     * Set the resource to use for the table.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Contracts\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>|null  $resource
     */
    public function setResource(Model|Builder|string|null $resource): void
    {
        if (\is_null($resource)) {
            return;
        }

        $this->resource = $resource;
    }

    /**
     * Guess the resource class name from the table class name.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    public function guessResource(): string
    {
        return (new Stringable(static::class))
            ->classBasename()
            ->beforeLast('Table')
            ->singular()
            ->prepend('\\App\\Models\\')
            ->value();
    }

    /**
     * Get the model class used by the resource.
     *
     * @throws \Honed\Table\Exceptions\MissingResourceException
     */
    public function getModel(): Model
    {
        return $this->getResource()->getModel();
    }

    /**
     * Get the model class as a name.
     *
     * @throws \Honed\Table\Exceptions\MissingResourceException
     */
    public function getModelName(): string
    {
        return \class_basename($this->getModel());
    }

    /**
     * Get the name of the model's primary key.
     *
     * @throws \Honed\Table\Exceptions\MissingResourceException
     */
    public function getModelKey(): string
    {
        return $this->getModel()->getKeyName();
    }
}
