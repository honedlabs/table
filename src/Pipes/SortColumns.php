<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;

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
        if ($this->isNotSortable()) {
            return;
        }

        foreach ($this->getHeadings() as $column) {
            $sort = $column->getSort();

            if ($sort) {
                $this->sort($sort);
            }
        }
    }
}
