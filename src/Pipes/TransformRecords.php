<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Action\Handlers\Concerns\Parameterisable;
use Honed\Action\Operations\InlineOperation;
use Honed\Core\Pipe;
use Honed\Table\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @template TClass of \Honed\Table\Table
 *
 * @extends Pipe<TClass>
 */
class TransformRecords extends Pipe
{
    use Parameterisable;

    /**
     * Run the after refining logic.
     */
    public function run(): void
    {
        $columns = $this->instance->getHeadings();

        $records = $this->instance->getRecords();

        $this->instance->setRecords(
            array_map(
                fn ($record) => $this->newRecord($record, $columns),
                $records
            )

        );
    }

    /**
     * Create a record for the table.
     *
     * @param  array<string, mixed>|Model  $record
     * @param  array<int, Column>  $columns
     * @return array<string, mixed>
     */
    protected function newRecord(array|Model $record, array $columns)
    {
        return [
            ...Arr::mapWithKeys(
                $columns,
                fn ($column) => $this->getColumn($record, $column)
            ),
            'class' => $this->instance->getClasses(
                $this->getNamedParameters($record), $this->getTypedParameters($record)
            ),
            '_key' => Arr::get($record, $this->instance->getKey()),
            'operations' => $this->instance->inlineOperationsToArray($record)
        ];
    }

    /**
     * Get the column value for a record.
     *
     * @param  array<string,mixed>|Model  $record
     * @return array<string, mixed>
     */
    protected function getColumn(array|Model $record, Column $column): array
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
}
