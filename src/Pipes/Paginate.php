<?php

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
        /**
         * @var \Illuminate\Support\Collection<array-key, array<array-key, mixed>> $records
         * @var array<string, array<array-key, mixed>> $meta
         */
        [$records, $meta] = $table->getPaginator()->paginate($table);
        $table->setRecords($records);
        $table->setMeta($meta);

        return $next($table);
    }
}
