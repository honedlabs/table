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
        $actions = $table->getInlineActions();
        $columns = $table->getCachedColumns();
        /** @var array<int,TModel> $records */
        $records = $table->getRecords();
        $serialize = $table->hasAttributes();

        $table->setRecords(
            \array_map(
                static fn ($record) => static::createRecord($record, $columns, $actions, $serialize),
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
     * @param  bool  $serialize
     * @return array<string,mixed>
     */
    public static function createRecord(
        $record,
        $columns,
        $actions,
        $serialize = false
    ) {
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

        $entry = $serialize ? $record->toArray() : [];

        $row = Arr::mapWithKeys(
            $columns,
            static function (Column $column) use ($record, $named, $typed) {
                $value = $column->hasValue()
                    ? $column->evaluate($column->getValue(), $named, $typed)
                    : Arr::get($record, $column->getName());

                return [
                    $column->getParameter() => [
                        'value' => $column->apply($value),
                        'extra' => $column->resolveExtra($named, $typed),
                    ],
                ];
            },
        );

        return \array_merge($entry, $row, ['actions' => $actions]);
    }
}
