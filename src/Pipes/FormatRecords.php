<?php

namespace App\Table\Pipes;

use Closure;
use Conquest\Table\Table;
use Illuminate\Support\Collection;
use Conquest\Table\Columns\BaseColumn;
use Illuminate\Database\Eloquent\Model;
use Conquest\Table\Pipes\Contracts\FormatsRecords;

/**
 * @internal
 */
class FormatRecords implements FormatsRecords
{
    public function handle(Table $table, Closure $next)
    {
        $table->setRecords($table->getRecords()->map(function ($record) use ($table) {
            return $table->getColumns()->reduce(function ($filteredRecord, BaseColumn $column) use ($record) {
                $columnName = $column->getName();
                $filteredRecord[$columnName] = $column->apply($record[$columnName] ?? null);

                return $filteredRecord;
            }, []);
        }));

        // $table->setRecords($table->getRecords()->map(function (Model $record) use ($table) {
            
        // }));

        return $next($table);
    }

    /**
     * @param mixed $record
     * @param Collection<int, \Conquest\Table\Columns\BaseColumn> $columns
     */
    protected function formatRecord($record, Collection $columns)
    {

    }

    protected function setActions($record, $actions)
    {

    }

    protected function setSelectable($record, bool|Closure $selectable)
    {


    }
}
