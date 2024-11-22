<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use ArrayAccess;
use Closure;
use Honed\Table\Table;
use Illuminate\Support\Collection;
use Honed\Table\Columns\BaseColumn;
use Illuminate\Database\Eloquent\Model;
use Honed\Table\Pipes\Contracts\FormatsRecords;
use Illuminate\Support\Facades\DB;

/**
 * @internal
 */
class FormatRecords implements FormatsRecords
{
    public function handle(Table $table, Closure $next)
    {
        // All columns
        $columns = $table->getColumns();
        // Whether to reduce the records to only the columns that are defined
        $enforceColumns = $table->enforcesColumns();
        // All inline actions
        $actions = $table->getInlineActions();
        
        $table->setRecords(
            $table->getRecords()->map(function ($record) use ($columns, $enforceColumns, $actions) {
                $formattedRecord = $enforceColumns ? [] : $record->toArray();
                
                // $this->applySelectable($record, $formattedRecord, $table);
                $this->applyColumns($record, $formattedRecord, $columns);
                $this->applyActions($record, $formattedRecord, $actions);
                
                return $formattedRecord;
            })
        );

        return $next($table);
    }

    /**
     * Apply the column formatter to this record.
     * 
     * @param Collection<BaseColumn> $columns
     */
    protected function applyColumns($originalRecord, &$formattedRecord, Collection $columns)
    {
        foreach ($columns as $column) {
            $columnName = $column->getName();
            $formattedRecord[$columnName] = $column->apply($originalRecord[$columnName] ?? null);
        }
    }

    /**
     * Set and authorize the available inline-actions for this record.
     */
    protected function applyActions($originalRecord, &$formattedRecord, Collection $actions)
    {
        $actions->each(function ($action) use (&$formattedRecord) {
            if ($action->isAuthorized()) {
                $formattedRecord['actions'][] = $action->toArray();
            }
        });
    }

    // /**
    //  * Determine if this record is selectable for bulk actions.
    //  */
    // protected function applySelectable($originalRecord, &$formattedRecord, Table $table)
    // {
    //     $formattedRecord['selectable'] = $table->isSelectable();
    // }
}
