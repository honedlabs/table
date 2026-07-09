<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Honed\Table\Table;

/**
 * @extends Pipe<\Honed\Table\Table>
 */
class SortColumns extends Pipe
{
    /**
     * Run the prepare columns logic.
     */
    public function run(Table $instance): void
    {
        if ($instance->isNotSortable()) {
            return;
        }

        foreach ($instance->getHeadings() as $column) {
            $sort = $column->getSort();

            if ($sort) {
                $instance->sort($sort);
            }
        }
    }
}
