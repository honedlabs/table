<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Honed\Table\Table;

/**
 * @extends Pipe<\Honed\Table\Table>
 */
class SearchColumns extends Pipe
{
    /**
     * Run the pipe logic.
     */
    public function run(Table $instance): void
    {
        if ($instance->isNotSearchable()) {
            return;
        }

        foreach ($instance->getColumns() as $column) {
            $search = $column->getSearch();

            if ($search) {
                $instance->search($search);
            }
        }
    }
}
