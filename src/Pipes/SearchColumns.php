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
class SearchColumns extends Pipe
{
    /**
     * Run the pipe logic.
     */
    public function run(): void
    {
        if ($this->instance->isNotSearchable()) {
            return;
        }

        $columns = $this->instance->getColumns();

        foreach ($columns as $column) {
            $this->search($column);
        }
    }

    /**
     * Prepare the column search state.
     */
    protected function search(Column $column): void
    {
        $search = $column->getSearch();

        if ($search) {
            $this->instance->search($search);
        }
    }
}
