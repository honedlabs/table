<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Actions\InlineAction;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasRecords
{
    /**
     * The records of the table retrieved from the resource.
     * 
     * @var \Illuminate\Support\Collection<array-key,array<array-key,mixed>>|null
     */
    protected $records = null;

    /**
     * Whether to reduce the records to only contain properties present in the columns.
     * 
     * @var bool
     */
    protected $reduce;

    /**
     * Whether to reduce the records to only contain properties present in the columns by default.
     * 
     * @var bool
     */
    protected static $defaultReduce = false;

    /**
     * Configure whether to reduce the records to only contain properties present in the columns by default.
     */
    public static function reduceRecords(bool $reduce = false): void
    {
        self::$defaultReduce = $reduce;
    }

    /**
     * Get the records of the table.
     *
     * @return \Illuminate\Support\Collection<int,array<string,mixed>>|null
     */
    public function getRecords(): ?Collection
    {
        return $this->records;
    }

    /**
     * Determine if the table has records.
     */
    public function hasRecords(): bool
    {
        return ! \is_null($this->records);
    }

    /**
     * Set the records of the table.
     * 
     * @param  \Illuminate\Support\Collection<int,array<string,mixed>>  $records
     */
    public function setRecords(Collection $records): void
    {
        $this->records = $records;
    }

    /**
     * Determine if the records should be reduced.
     */
    public function isReducing(): bool
    {
        return match (true) {
            \property_exists($this, 'reduce') && !\is_null($this->reduce) => (bool) $this->reduce,
            \method_exists($this, 'reduce') => (bool) $this->reduce(),
            default => static::$defaultReduce,
        };
    }

    /**
     * Format the records using the provided columns.
     * 
     * @param \Illuminate\Support\Collection<int,\Honed\Table\Columns\BaseColumn> $activeColumns
     * @param \Illuminate\Support\Collection<int,\Honed\Table\Actions\InlineAction> $inlineActions
     */
    public function formatRecords(Collection $records, Collection $activeColumns, Collection $inlineActions = null, mixed $selectableEvaluation = null)
    {
        if ($records->isEmpty()) {
            return $records;
        }

        $columnsMap = $activeColumns->keyBy(fn ($column) => $column->getName());
        $reducing = $this->isReducing();

        return $records->map(function ($record) use ($inlineActions, $selectableEvaluation, $columnsMap, $reducing) {
            $formattedRecord = $reducing ? [] : (\is_array($record) ? $record : $record->toArray());

            if (! \is_null($inlineActions) && $inlineActions->isNotEmpty()) {
                $formattedRecord['actions'] = $inlineActions
                    ->filter(fn ($action) => $action->isAuthorized([
                        'record' => $record,
                        'model' => $record,
                        'product' => $record,
                    ], [
                        Product::class => $record,
                        Model::class => $record,
                    ]))
                    ->values();
            }

            $formattedRecord['selectable'] = $selectableEvaluation ? (bool) $selectableEvaluation($record) : false;

            // Format columns
            foreach ($columnsMap as $name => $column) {
                $value = $this->accessRecord($record, $name);
                $formattedRecord[$name] = $column->format($value, $record);
            }

            return $formattedRecord;
        });
    }

    protected function accessRecord(mixed $record, string $property): mixed
    {
        return match (true) {
            is_array($record) => $record[$property] ?? null,
            default => $record->{$property} ?? null,
        };
    }

    protected function resolveActions(Collection $actions, mixed $record): Collection
    {
        return $actions
            ->filter(fn (InlineAction $action) => $action->isAuthorized($record))
            ->each(fn (InlineAction $action) => $action->link->resolveLink([
                'record' => $record,
            ], [
                
            ]))
            ->values();
    }
}
