<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Action\Handlers\Concerns\Parameterisable;
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
        $columns = $this->getHeadings();

        $records = $this->getRecords();

        $this->setRecords(
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
    protected function newRecord(array|Model $record, array $columns): array
    {
        return [
            ...Arr::mapWithKeys(
                $columns,
                fn ($column) => [
                    $column->getParameter() => $column->generate($record),
                ]
            ),
            'class' => $this->getClasses(
                $this->getNamedParameters($record), $this->getTypedParameters($record)
            ),
            '_key' => Arr::get($record, $this->getKey()),
            'operations' => $this->inlineOperationsToArray($record),
        ];
    }
}
