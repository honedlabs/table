<?php

namespace Honed\Table\Concerns;

use RuntimeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait HasResource
{
    /**
     * @var \Illuminate\Contracts\Database\Eloquent\Builder|class-string
     */
    protected $resource;

    /**
     * Set the resource to use for the table.
     * 
     * @param \Illuminate\Contracts\Database\Eloquent\Builder|class-string|null $resource
     */
    public function setResource($resource)
    {
        if (is_null($resource)) {
            return;
        }

        $this->resource = $resource;
    }

    /**
     * Get the resource to use for the table.
     * @internal
     * @throws \RuntimeException
     * @return \Illuminate\Contracts\Database\Eloquent\Builder|class-string
     */
    protected function definedResource()
    {
        if (isset($this->resource)) {
            return $this->resource;
        }

        if (method_exists($this, 'resource')) {
            return $this->resource();
        }

        // Else, try to resolve a model from table name
        $modelClass = str(static::class)
            ->classBasename()
            ->beforeLast('Table')
            ->singular()
            ->prepend('\\App\\Models\\')
            ->toString();

        if (class_exists($modelClass)) {
            return $modelClass;
        }

        throw new RuntimeException(sprintf('Unable to resolve resource for [%s]', static::class));
    }

    /**
     * Get the resource to use for the table as an Eloquent query builder.
     * 
     * @throws \RuntimeException
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getResource()
    {
        if (!isset($this->resource)) {
            $this->resource ??= $this->definedResource();
        }

        if ($this->resource instanceof Builder) {
            return $this->resource;
        }

        if ($this->resource instanceof Model) {
            return $this->resource->newQuery();
        }

        if (is_string($this->resource)) {
            return $this->resource::query();
        }

        throw new RuntimeException(sprintf('[%s] requires a class-string, model or Eloquent resource.', static::class));
    }

    /**
     * Get the fully qualified path of the resource.
     * 
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    public function getResourceModel()
    {
        return $this->getResource()->getModel()::class;
    }

    /**
     * Get the base class name of the resource.
     * 
     * @return string
     */
    public function getResourceName()
    {
        return class_basename($this->getResourceModel());
    }
}
