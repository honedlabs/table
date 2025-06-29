<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;
use Honed\Infolist\Entries\Concerns\HasClasses as HasHeadingClasses;

trait HasCellClasses
{
    use HasHeadingClasses;

    /**
     * The classes to apply to an individual cell.
     *
     * @var array<int, string|Closure(mixed...):string>
     */
    protected $cellClasses = [];

    /**
     * Set the classes to apply to an individual cell.
     *
     * @param  string|Closure(mixed...):string  $classes
     * @return $this
     */
    public function cells($classes)
    {
        $this->cellClasses[] = $classes;

        return $this;
    }

    /**
     * Get the classes to apply to an individual cell.
     *
     * @return string|null
     */
    public function getCellClasses()
    {
        return $this->createClasses($this->cellClasses);
    }
}
