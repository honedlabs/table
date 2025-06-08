<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Table\Table;

class CleanupTable
{
    /**
     * Cleanup the table.
     *
     * @template T of \Honed\Table\Table
     *
     * @param  T  $table
     * @param  \Closure(T):T  $next
     * @return T
     */
    public function __invoke($table, $next)
    {
        $table->flushCachedColumns();

        return $next($table);
    }
}
