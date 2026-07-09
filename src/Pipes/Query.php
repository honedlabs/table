<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Honed\Table\Table;

/**
 * @extends Pipe<\Honed\Table\Table>
 */
class Query extends Pipe
{
    /**
     * Run the query logic.
     */
    public function run(Table $instance): void
    {
        foreach ($instance->getHeadings() as $heading) {
            $callback = $heading->queryCallback();

            if ($callback) {
                $instance->evaluate($callback);
            }
        }
    }
}
