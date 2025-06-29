<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Action\Operations\InlineOperation;
use Honed\Core\Pipe;
use Illuminate\Database\Eloquent\Model;
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
     */
    public function run(): void
    {
        $instance = $this->instance;

        $columns = $instance->getHeadings();
        $operations = $instance->getInlineOperations();
        $records = $instance->getRecords();

        $processedRecords = array_map(
            fn ($record) => $this->create($record, $instance, $columns, $operations),
            $records
        );

        $instance->setRecords($processedRecords);
    }

    /**
     * Create a record for the table.
     *
     * @param  array<string, mixed>|Model  $record
     * @param  TClass  $instance
     * @param  array<int, \Honed\Table\Columns\Column>  $columns
     * @param  array<int, InlineOperation>  $operations
     * @return array<string, mixed>
     */
    protected function create($record, $instance, $columns, $operations)
    {
        return [
            ...$this->getColumns($record, $columns),
            'class' => $instance->getClasses($this->named($record), $this->typed($record)),
            'operations' => $this->getOperations($record, $operations),
        ];
    }

    /**
     * Get the operations for a record.
     *
     * @param  array<string, mixed>|Model  $record
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
     * @param  array<string, mixed>|Model  $record
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
     * @param  array<string,mixed>|Model  $record
     * @param  \Honed\Table\Columns\Column  $column
     * @return array<string, mixed>
     */
    protected function getColumn($record, $column)
    {
        [$value, $placeholder] = $column->value($record);

        return [
            $column->getParameter() => [
                'v' => $value,
                'e' => $column->getExtra(),
                'c' => $column->getCellClasses(),
                'f' => $placeholder,
            ],
        ];
    }

    /**
     * Get the typed evaluators for a record.
     *
     * @param  array<string, mixed>|Model  $record
     * @return array<class-string, mixed>
     */
    protected function typed($record)
    {
        if ($record instanceof Model) {
            return array_fill_keys([Model::class, $record::class], $record);
        }

        return [];
    }

    /**
     * Get the named evaluators for a record.
     *
     * @param  array<string, mixed>|Model  $record
     * @return array<string, mixed>
     */
    protected function named($record)
    {
        return array_fill_keys(['model', 'record', 'row'], $record);
    }
}
