<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;

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
        if ($this->isNotFilterable()) {
            return;
        }

        foreach ($this->getColumns() as $column) {
            $filter = $column->getFilter();

            if ($filter) {
                $this->filter($filter);
            }
        }
    }
}
