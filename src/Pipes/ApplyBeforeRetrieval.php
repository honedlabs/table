<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Table\Pipes\Contracts\BeforeRetrieval;
use Honed\Table\Table;

/**
 * @internal
 */
class ApplyBeforeRetrieval implements BeforeRetrieval
{
    public function handle(Table $table, \Closure $next)
    {
        if (method_exists($table, 'beforeRetrieval')) {
            $table->beforeRetrieval($table->getResource());
        }

        return $next($table);
    }
}
