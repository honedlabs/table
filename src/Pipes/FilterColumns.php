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
class FilterColumns extends Pipe
{
    /**
     * Run the pipe logic.
     */
    public function run(): void
    {
        if ($this->instance->isNotFilterable()) {
            return;
        }

        $columns = $this->instance->getColumns();

        foreach ($columns as $column) {
            $this->filter($column);
        }
    }

    /**
     * Prepare the column sort state.
     */
    protected function filter(Column $column): void
    {
        $filter = $column->getFilter();

        if ($filter) {
            $this->instance->filter($filter);
        }
    }
}
