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
    public function class(...$class)
    {
        $this->class = \array_merge(
            (array) $this->class,
            $class
        );

        return $this;
    }

    /**
     * Determine if the column has class.
     *
     * @return bool
     */
    public function hasClass()
    {
        return filled($this->class);
    }

    /**
     * Get the class for the column.
     *
     * @return string|null
     */
    public function getClass()
    {
        if (! $this->hasClass()) {
            return null;
        }

        return Arr::toCssClasses($this->class);
    }
}
