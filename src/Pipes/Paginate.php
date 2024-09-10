<?php

namespace App\Table\Pipes;

use Closure;
use Conquest\Table\Pagination\Enums\Paginator;
use Conquest\Table\Pipes\Contracts\Paginates;
use Conquest\Table\Table;

/**
 * @internal
 */
class Paginate implements Paginates
{
    public function handle(Table $table, Closure $next)
    {
        /** @var array{0: \Illuminate\Support\Collection, 1: array<string, array-key>} */
        [$records, $meta] = $table->getPaginator()->paginate($table);
        $table->setRecords($records);
        $table->setMeta($meta);

        return $next($table);
    }
}
