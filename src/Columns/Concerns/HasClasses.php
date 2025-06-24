<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;
use Honed\Infolist\Entries\Concerns\HasClasses as HasHeadingClasses;

trait HasClasses
{
    use HasHeadingClasses;

    /**
     * The classes to apply to an individual cell.
     *
     * @var array<int, string|Closure(mixed...):string>
     */
    protected $cellClasses = [];

    /**
     * The classes to apply to the record (row).
     *
     * @var array<int, string|Closure(mixed...):string>
     */
    protected $recordClasses = [];

    /**
     * Set the classes to apply to an individual cell.
     *
     * @param  string|Closure(mixed...):string  $classes
     * @return $this
     */
    public function cellClasses($classes)
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
        return $this->createClass($this->cellClasses);
    }

    /**
     * Set the classes to apply to the record (row).
     *
     * @param  string|Closure(mixed...):string  $classes
     * @return $this
     */
    public function recordClasses($classes)
    {
        $this->recordClasses[] = $classes;

        return $this;
    }

    /**
     * Get the classes to apply to the record (row).
     *
     * @return string|null
     */
    public function getRecordClasses()
    {
        return $this->createClass($this->recordClasses);
    }
}
