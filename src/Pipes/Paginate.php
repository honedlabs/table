<?php

namespace App\Table\Pipes;

use Closure;
use Conquest\Table\Pipes\Contracts\Paginates;
use Conquest\Table\Table;

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
