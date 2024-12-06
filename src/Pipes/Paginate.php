<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Closure;
use Honed\Table\Pagination\Enums\Paginator;
use Honed\Table\Pipes\Contracts\Paginates;
use Honed\Table\Table;

/**
 * @internal
 */
class Paginate implements Paginates
{
    public function handle(Table $table, Closure $next)
    {
        $data = $table->paginateRecords($table->getQuery());
        
        // $table->setRecords($data);
        // $table->setMeta();

        return $next($table);
    }
}
