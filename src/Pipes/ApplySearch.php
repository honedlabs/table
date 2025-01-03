<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Table\Pipes\Contracts\Searches;
use Honed\Table\Table;

/**
 * @internal
 */
class ApplySearch implements Searches
{
    public function handle(Table $table, \Closure $next)
    {
        $builder = $table->getResource();
        $searches = array_merge(
            $table->getSearch(),
            $table->getSearchableColumns()->toArray()
        );
        // Handle scout case

        return $next($table);
    }
}
