<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Action\Handlers\Concerns\Parameterisable;
use Honed\Core\Pipe;
use Honed\Infolist\Entries\Entry;
use Honed\Table\Contracts\Column;
use Honed\Table\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @extends Pipe<\Honed\Table\Table>
 */
class TransformRecords extends Pipe
{
    use Parameterisable;

    /**
     * Run the after refining logic.
     */
    public function run(Table $instance): void
    {
        $columns = $instance->getHeadings();

        $records = $instance->getRecords();

        $instance->setRecords(
            array_map(
                fn ($record) => $this->newRecord($instance, $record, $columns),
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
    protected function newRecord(Table $instance, array|Model $record, array $columns): array
    {
        return [
            ...Arr::mapWithKeys(
                $columns,
                fn (Column $column) => [
                    $column->getParameter() => $column instanceof Entry
                        ? $column->generate($record)
                        : [],
                ]
            ),
            'class' => $instance->getClasses(
                $this->getNamedParameters($record), $this->getTypedParameters($record)
            ),
            '_key' => Arr::get($record, $instance->getKey()),
            'operations' => $instance->inlineOperationsToArray($record),
        ];
    }
}
