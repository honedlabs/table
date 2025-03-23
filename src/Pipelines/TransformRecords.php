<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Action\InlineAction;
use Honed\Table\Columns\Column;
use Honed\Table\Table;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class TransformRecords
{
    /**
     * Transform the records.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        /** @var array<int,TModel> */
        $records = $table->getRecords();

        $table->setRecords(
            \array_map(
                static fn ($record) => static::createRecord(
                    $record,
                    $table->getCachedColumns(),
                    $table->getInlineActions(),
                    $table->hasAttributes()
                ),
                $records
            )
        );

        return $next($table);
    }

    /**
     * Create a record for the table.
     *
     * @param  TModel  $record
     * @param  array<int,\Honed\Table\Columns\Column<TModel, TBuilder>>  $columns
     * @param  array<int,\Honed\Action\InlineAction>  $actions
     * @param  bool  $attr
     * @return array<string,mixed>
     */
    public static function createRecord($record, $columns, $actions, $attr)
    {
        [$named, $typed] = Table::getModelParameters($record);

        $actions = \array_map(
            static fn (InlineAction $action) => $action->resolveToArray($named, $typed),
            \array_values(
                \array_filter(
                    $actions,
                    static fn (InlineAction $action) => $action->isAllowed($named, $typed)
                )
            )
        );

        $entry = $attr ? $record->toArray() : [];

        $row = Arr::mapWithKeys(
            $columns,
            static fn (Column $column) => $column->createEntry($record, $named, $typed)
        );

        return \array_merge($entry, $row, ['actions' => $actions]);
    }
}
