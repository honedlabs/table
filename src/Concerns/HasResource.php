<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait HasResource
{
    /**
     * @var class-string<\Illuminate\Database\Eloquent\Model>|null $resource
     */
    protected $resource;

    /**
     * Get the resource of the table.
     * 
     * @return class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
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
     * Guess an Eloquent model to use for the table based on the class name.
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
}
