<?php

namespace Honed\Table\Concerns;

use RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Honed\Core\Concerns\Inspectable
 */
trait Resourceful
{
    /**
     * @var \Illuminate\Contracts\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>|string
     */
    protected $resource;

    /**
     * @var \Closure(class-string,string|int):\Illuminate\Database\Eloquent\Model
     */
    protected \Closure $modelResolver;

    /**
     * Define the database resource to use for the table.
     * 
     * @param \Illuminate\Contracts\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>|string $resource
     * @return $this
     */
    public function resource($resource): static
    {
        $this->setResource($resource);

        return $this;
    }

    /**
     * Set the model resolver to use for the table.
     * 
     * @param \Closure(class-string, string|int): \Illuminate\Database\Eloquent\Model $modelResolver
     * @return $this
     */
    public function modelResolver(\Closure $modelResolver): static
    {
        $this->setResolver($modelResolver);

        return $this;
    }

    /**
     * Set the resource to use for the table.
     * 
     * @param \Illuminate\Contracts\Database\Eloquent\Builder|class-string|null $resource
     */
    public function setResource($resource)
    {
        if (\is_null($resource)) {
            return;
        }

        $this->resource = $resource;
    }

    /**
     * Set the resolver to use for the table.
     * 
     * @param (\Closure(class-string,string|int):\Illuminate\Database\Eloquent\Model)|null $modelResolver
     */
    public function setResolver(\Closure|null $modelResolver)
    {
        if (\is_null($modelResolver)) {
            return;
        }

        $this->modelResolver = $modelResolver;
    }

    /**
     * Get the resource to use for the table as an Eloquent query builder.
     * 
     * @throws \RuntimeException
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getResource()
    {
        // @phpstan-ignore-next-line
        $this->resource ??= $this->inspect('resource', static fn () => throw new RuntimeException(sprintf('[%s] requires a class-string, model or Eloquent resource.', static::class)));

        return match (true) {
            $this->resource instanceof Builder => $this->resource,
            $this->resource instanceof Model => $this->resource->newQuery(),
            is_string($this->resource) => $this->resource::query(),
            default => throw new RuntimeException(sprintf('[%s] requires a class-string, model or Eloquent resource.', static::class)),
        };
    }

    /**
     * Resolve a model instance from the given key.
     * 
     * @param int|string $key
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function resolveModel(int|string $key): Model
    {
        $modelClass = $this->getModelClass();
        $resolver = $this->modelResolver ?? fn (string $modelClass, int|string $key) => $modelClass::findOrFail($key);

        return \call_user_func($resolver, $modelClass, $key);
    }

    /**
     * Get the model class used by the resource.
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModelClass(): Model
    {
        return $this->getResource()->getModel();
    }

    /**
     * Get the model class as a name.
     * 
     * @return string
     */
    public function getModelClassName(): string
    {
        return strtolower(class_basename($this->getModelClass()));
    }

    // public function getKeyName(): string
    // {
    //     return $this->getResource()->getKeyName();
    // }
}
