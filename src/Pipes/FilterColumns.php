<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Honed\Table\Table;

/**
 * @extends Pipe<\Honed\Table\Table>
 */
class FilterColumns extends Pipe
{
    /**
     * Run the pipe logic.
     */
    public function run(Table $instance): void
    {
        if ($instance->isNotFilterable()) {
            return;
        }

        foreach ($instance->getColumns() as $column) {
            $filter = $column->getFilter();

            if ($filter) {
                $instance->filter($filter);
            }
        }
    }
}
