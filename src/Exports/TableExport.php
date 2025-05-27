<?php

namespace Honed\Table\Exports;

use Honed\Table\Contracts\ExportsTable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TableExport implements ExportsTable, WithStrictNullComparison, ShouldAutoSize
{
    use Exportable;

    /**
     * The query to export.
     * 
     * @var \Honed\Table\Table
     */
    protected $table;

    /**
     * Create a new table exporter.
     * 
     * @param  \Honed\Table\Table  $table
     * @return static
     */
    public static function from($table)
    {
        return resolve(static::class)
            ->table($table);
    }

    /**
     * Set the table to export.
     * 
     * @param  \Honed\Table\Table  $table
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return;
    }

    public function map($row): array
    {
        return [];
    }

    public function headings(): array
    {
        return [];
    }

    public function events()
    {
        
    }

    public function registerEvents(): array
    {
        return [];
    }

    public function columns($columns)
    {
        return;
    }
}