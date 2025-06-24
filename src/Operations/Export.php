<?php

declare(strict_types=1);

namespace Honed\Table\Operations;

use Closure;
use Honed\Action\Contracts\Action;
use Honed\Action\Operations\Operation;
use Honed\Table\Contracts\ExportsTable;
use Honed\Table\Exporters\ArrayExporter;
use Honed\Table\Exporters\Concerns\HasExportEvents;
use Honed\Table\Exporters\EloquentExporter;
use Honed\Table\Table;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class Export extends Operation implements Action
{
    use Concerns\CanLimitRecords;
    use Concerns\Exportable;
    use HasExportEvents;
    // use HasExport;

    /**
     * The callback to be used to create the export from the table.
     *
     * @var (Closure(Table, ExportsTable, \Illuminate\Http\Request):mixed)|null
     */
    protected $using;

    /**
     * The exporter to be used for the export.
     *
     * @var class-string<ExportsTable>|null
     */
    protected $exporter;

    /**
     * The registered callback to be called if the export is not downloaded.
     *
     * @var callable
     */
    protected $after;

    /**
     * Register the callback to be used to create the export from the table.
     *
     * @param  (Closure(Table, ExportsTable, \Illuminate\Http\Request):mixed)|null  $callback
     * @return $this
     */
    public function using($callback)
    {
        $this->using = $callback;

        return $this;
    }

    /**
     * Get the callback to be used to create the export from the table.
     *
     * @return (Closure(Table, ExportsTable, \Illuminate\Http\Request):mixed)|null
     */
    public function getUsingCallback()
    {
        return $this->using;
    }

    /**
     * Set the exporter class to be used to generate the export.
     *
     * @param  class-string<ExportsTable>|null  $exporter
     * @return $this
     */
    public function exporter($exporter)
    {
        $this->exporter = $exporter;

        return $this;
    }

    /**
     * Get the exporter class to be used to generate the export.
     *
     * @param  Table  $table
     * @return class-string<ExportsTable>
     */
    public function getExporter($table)
    {
        return $this->exporter ?? $this->getEloquentExporter();
        // return $this->exporter ?? match (true) {
        //     false => $this->getArrayExporter(),
        //     default => $this->getEloquentExporter()
        // };
    }

    /**
     * Handle the exporting of the table.
     *
     * @return mixed
     */
    public function handle(Table $table)
    {
        $exporter = $this->getExporter($table);

        $export = new $exporter($table, $this->getEvents());

        $filename = $this->getFilename();

        if ($use = $this->getUsingCallback()) {
            return $this->evaluate($use);
        }

        $response = match (true) {
            $this->isDownload() => Excel::download($export, $filename, $this->getFileType()),
            $this->isQueued() => Excel::queue(
                $export, $filename, $this->getDisk(), $this->getFileType()
            )->onQueue($this->getQueue()),
            default => Excel::store(
                $export, $filename, $this->getDisk(), $this->getFileType()
            ),
        };

        return $response;
    }

    /**
     * Get the eloquent exporter class to be used to generate the export.
     *
     * @return class-string<ExportsTable>
     */
    protected function getEloquentExporter()
    {
        /** @var class-string<ExportsTable> */
        return Config::get('table.exporters.eloquent', EloquentExporter::class);
    }

    /**
     * Get the array exporter class to be used to generate the export.
     *
     * @return class-string<ExportsTable>
     */
    protected function getArrayExporter()
    {
        /** @var class-string<ExportsTable> */
        return Config::get('table.exporters.array', ArrayExporter::class);
    }
}
