<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Illuminate\Support\Arr;

trait HasClass
{
    /**
     * @var array<mixed,mixed>
     */
    protected $class = [];

    /**
     * Set the class for the column.
     *
     * @param  array<string>  $class
     * @return $this
     */
    public function class(...$class): static
    {
        $this->class = \array_merge(
            (array) $this->class,
            $class
        );

        return $this;
    }

    /**
     * Determine if the column has class.
     */
    public function hasClass(): bool
    {
        return \count($this->class) > 0;
    }

    /**
     * Get the class for the column.
     */
    public function getClass(): ?string
    {
        if (! $this->hasClass()) {
            return null;
        }

        return Arr::toCssClasses($this->class);
    }
}
