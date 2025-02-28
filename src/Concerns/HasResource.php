<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasResource
{
    /**
     * @var class-string<\Illuminate\Database\Eloquent\Model>|null
     */
    protected $resource;

    /**
     * Get the resource of the table.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function getResource()
    {
        return match (true) {
            isset($this->resource) => $this->resource,
            \method_exists($this, 'resource') => $this->resource(),
            default => $this->guessResource()
        };
    }

    /**
     * Guess an Eloquent model to use for the table based on the class name.
     *
     * @return string
     */
    public function guessResource()
    {
        return str(static::class)
            ->classBasename()
            ->beforeLast('Table')
            ->singular()
            ->prepend('\\App\\Models\\')
            ->value();
    }
}
