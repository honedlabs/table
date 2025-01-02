<?php

namespace Honed\Table\Concerns;

use Closure;
use RuntimeException;
use Illuminate\Support\Stringable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Honed\Table\Exceptions\MissingResourceException;

trait HasResource
{
    /**
     * The model class-string, or Eloquent Builder instance to use for the table.
     * 
     * @var \Illuminate\Contracts\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>|string
     */
    // protected $resource;

    /**
     * Modify the resource query before it is used on a per controller basis.
     * 
     * @var (\Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder)|null)|null
     */
    protected $resourceModifier = null;

    /**
     * Get the resource to use for the table as an Eloquent query builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \RuntimeException
     */
    public function getResource()
    {
        if (! isset($this->resource)) {
            $this->resource = match (true) {
                \method_exists($this, 'resource') => $this->resource(),
                \property_exists($this, 'resource') => $this->resource,
                default => $this->guessResourceFromTable()
            };
        }

        $this->setResource(match (true) {
            $this->resource instanceof Builder => null, // do nothing
            $this->resource instanceof Model => $this->resource->newQuery(),
            \is_string($this->resource) => $this->resource::query(),
            default => throw new MissingResourceException(static::class)
        });

        return $this->resource;
    }

    /**
     * Set the resource to use for the table.
     *
     * @param  \Illuminate\Contracts\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>|null  $resource
     */
    public function setResource(Model|Builder|Closure|string|null $resource): void
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
    protected function guessResourceFromTable(): string 
    {
        return (new Stringable(static::class))
            ->classBasename()
            ->beforeLast('Table')
            ->singular()
            ->prepend('\\App\\Models\\')
            ->value();
    }

    /**
     * Retrieve the resource modifier.
     * 
     * @return (\Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder|null))|null
     */
    public function getResourceModifier(): ?Closure
    {
        return $this->resourceModifier;
    }

    /**
     * Determine if the table has a resource modifier.
     */
    public function hasResourceModifier(): bool
    {
        return ! \is_null($this->resourceModifier);
    }

    /**
     * Set the resource modifier.
     * 
     * @param  \Closure(\Illuminate\Database\Eloquent\Builder):(\Illuminate\Database\Eloquent\Builder)  $resourceModifier
     */
    public function setResourceModifier(Closure $resourceModifier): void
    {
        $this->resourceModifier = $resourceModifier;
    }

    /**
     * Get the model class used by the resource.
     * 
     * @throws \Honed\Table\Exceptions\MissingResourceException
     * @return \Illuminate\Database\Eloquent\Model
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
        return (new Stringable($this->getModel()))
            ->classBasename()
            ->value();
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
