<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Honed\Table\Columns\Column;

/**
 * @template TClass of \Honed\Table\Table
 *
 * @extends Pipe<TClass>
 */
class SortColumns extends Pipe
{
    /**
     * Run the prepare columns logic.
     */
    public function run(): void
    {
        if ($this->instance->isNotSortable()) {
            return;
        }

        $columns = $this->instance->getHeadings();

        foreach ($columns as $column) {
            $this->sort($column);
        }
    }

    /**
     * Prepare the column sort state.
     */
    protected function sort(Column $column): void
    {
        $sort = $column->getSort();

        if ($sort) {
            $this->instance->sort($sort);
        }
    }
}
