<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Generator;
use Honed\Action\Operations\InlineOperation;
use Honed\Core\Pipe;
use Illuminate\Support\Arr;

/**
 * @template TClass of \Honed\Table\Table
 *
 * @extends Pipe<TClass>
 */
class TransformRecords extends Pipe
{
    /**
     * Run the after refining logic.
     *
     * @param  TClass  $instance
     * @return void
     */
    public function run($instance)
    {
        $columns = $instance->getHeadings();
        $operations = $instance->getInlineOperations();
        $records = $instance->getRecords();

        $processedRecords = iterator_to_array(
            $this->generator($records, $columns, $operations)
        );

        $instance->setRecords($processedRecords);
    }

    /**
     * Create a generator that yields processed records.
     *
     * @param  array<int, array<string, mixed>|\Illuminate\Database\Eloquent\Model>  $records
     * @param  array<int, \Honed\Table\Columns\Column>  $columns
     * @param  array<int, InlineOperation>  $operations
     * @return Generator<int, array<string, mixed>>
     */
    protected function generator($records, $columns, $operations)
    {
        foreach ($records as $record) {
            yield $this->create($record, $columns, $operations);
        }
    }

    /**
     * Create a record for the table.
     *
     * @param  array<string, mixed>|\Illuminate\Database\Eloquent\Model  $record
     * @param  array<int, \Honed\Table\Columns\Column>  $columns
     * @param  array<int, InlineOperation>  $operations
     * @return array<string, mixed>
     */
    protected function create($record, $columns, $operations)
    {
        return [
            ...$this->getColumns($record, $columns),
            'operations' => $this->getOperations($record, $operations),
        ];
    }

    /**
     * Get the operations for a record.
     *
     * @param  array<string, mixed>|\Illuminate\Database\Eloquent\Model  $record
     * @param  array<int, InlineOperation>  $operations
     * @return array<int, array<string, mixed>>
     */
    protected function getOperations($record, $operations)
    {
        return array_map(
            static fn (InlineOperation $operation) => $operation->record($record)->toArray(),
            array_values(
                array_filter(
                    $operations,
                    static fn (InlineOperation $operation) => $operation->record($record)->isAllowed()
                )
            )
        );
    }

    /**
     * Get the column values for a record.
     *
     * @param  array<string, mixed>|\Illuminate\Database\Eloquent\Model  $record
     * @param  array<int, \Honed\Table\Columns\Column>  $columns
     * @return array<string, array<string, mixed>>
     */
    protected function getColumns($record, $columns)
    {
        return Arr::mapWithKeys(
            $columns,
            fn ($column) => $this->getColumn($record, $column)
        );
    }

    /**
     * Get the column value for a record.
     *
     * @param  array<string,mixed>|\Illuminate\Database\Eloquent\Model  $record
     * @param  \Honed\Table\Columns\Column  $column
     * @return array<string, mixed>
     */
    protected function getColumn($record, $column)
    {
        [$value, $placeholder] = $column->value($record);

        return [$column->getParameter() => [
            'v' => $value,
            'e' => $column->getExtra(),
            'c' => $column->getCellClasses(),
            'f' => $placeholder,
        ],
        ];
    }
}
