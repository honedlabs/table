<?php

declare(strict_types=1);

namespace Honed\Table\Exporters;

use Closure;
use Honed\Table\Contracts\ExportsTable;
use Honed\Table\Table;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class Exporter implements ExportsTable, ShouldAutoSize, WithStrictNullComparison
{
    use Concerns\HasExportEvents;
    use Exportable;

    /**
     * The query to export.
     *
     * @var Table
     */
    protected $table;

    /**
     * The headings to export, cached.
     *
     * @var array<int, \Honed\Table\Columns\Column>|null
     */
    protected $headings;

    /**
     * Create a new table export.
     *
     * @param  array<class-string<\Maatwebsite\Excel\Events\Event>, callable>  $events
     * @return void
     */
    public function __construct(Table $table, array $events = [])
    {
        $this->table($table);
        $this->events($events);
    }

    /**
     * Set the table to export.
     *
     * @param  Table  $table
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the headings for the export.
     *
     * @return array<int, string|null>
     */
    public function headings(): array
    {
        return array_map(
            static fn ($heading) => $heading->getLabel(),
            $this->getHeadings()
        );
    }

    /**
     * Map the records to the export.
     *
     * @param  array<string, mixed>|\Illuminate\Database\Eloquent\Model  $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        $this->getHeadings();

        return array_map(
            static fn ($heading) => $heading->value($row)[0],
            $this->getHeadings()
        );
    }

    /**
     * Get the styles for the export.
     *
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        $columns = $this->getHeadings();
        $index = 'A';

        foreach ($columns as $column) {
            $style = $column->getExportStyle();

            match (true) {
                is_array($style) => $sheet->getStyle($index)->applyFromArray($style),
                $style instanceof Closure => $style($sheet->getStyle($index)),
                default => null,
            };

            // Also apply exportFormat if set
            $format = $column->getExportFormat();
            if ($format) {
                $sheet->getStyle($index)->getNumberFormat()->setFormatCode($format);
            }

            $index++;
        }
    }

    /**
     * Register the events for the export.
     *
     * @return array<class-string<\Maatwebsite\Excel\Events\Event>, callable>
     */
    public function registerEvents(): array
    {
        return $this->events;
    }

    /**
     * Get the column headings.
     *
     * @return array<int, \Honed\Table\Columns\Column>
     */
    protected function getHeadings(): array
    {
        return $this->headings ??= array_values(
            array_filter(
                $this->table->getHeadings(),
                static fn ($heading) => $heading->isExportable()
            )
        );
    }
}
