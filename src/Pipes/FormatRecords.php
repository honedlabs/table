<?php

namespace App\Table\Pipes;

use Closure;
use Conquest\Table\Pipes\Contracts\FormatsRecords;
use Conquest\Table\Table;
use Illuminate\Support\Collection;

/**
 * @internal
 */
class FormatRecords implements FormatsRecords
{
    public function handle(Table $table, Closure $next)
    {
        $table->setRecords($table->getRecords()->map(function ($record) use ($table) {
            return $table->getTableColumns()->reduce(function ($filteredRecord, BaseColumn $column) use ($record) {
                $columnName = $column->getName();
                $filteredRecord[$columnName] = $column->apply($record[$columnName] ?? null);

                return $filteredRecord;
            }, []);
        }));

        return $next($table);
    }

    /**
     * @param mixed $record
     * @param Collection<BaseColumn> $columns
     */
    protected function formatRecord($record, Collection $columns)
    {

    }
}
