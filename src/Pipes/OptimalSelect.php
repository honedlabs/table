<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Table\Pipes\Contracts\SelectsOptimal;
use Honed\Table\Table;

/**
 * @internal
 */
class OptimalSelect implements SelectsOptimal
{
    public function handle(Table $table, \Closure $next)
    {
        // if ((bool) $table->inspect('optimalSelect', false)) {
        //     $table->optimalSelect();
        // }

        return $next($table);
    }
}
