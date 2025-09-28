<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;

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
        if ($this->isNotSearchable()) {
            return;
        }

        foreach ($this->getColumns() as $column) {
            $search = $column->getSearch();

            if ($search) {
                $this->search($search);
            }
        }
    }
}
