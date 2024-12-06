<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Table\Pipes\Contracts\SelectsRecords;
use Honed\Table\Table;

/**
 * @internal
 */
class SelectRecords implements SelectsRecords
{
    public function handle(Table $table, \Closure $next)
    {
        if ($table->isAutomaticSelecting()) {
            $keyColumn = $table->getKeyColumn();
            $columnsToSelect = [$keyColumn->getName(), ...$table->getColumns()->pluck('name')];
            $table->getResource()->select(...$columnsToSelect);
        }

        return $next($table);
    }
}
