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
use Honed\Table\Exporters\Exporter;
use Honed\Table\Table;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;

class Export extends Operation implements Action
{
    use Concerns\CanLimitRecords;
    use Concerns\HasExport;
    use HasExportEvents;

    /**
     * The callback to be used to create the export from the table.
     *
     * @var (Closure(mixed...):mixed)|null
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
     * @param  (Closure(mixed...):mixed)|null  $callback
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
     * @return (Closure(mixed...):mixed)|null
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
        if ($use = $this->getUsingCallback()) {
            return $this->useCallback($use, $table);
        }

        $exporter = $this->getExporter($table);

        $export = new $exporter($table, $this->getEvents());

        $fileName = $this->getFilename();

        return match (true) {
            $this->isDownload() => $this->downloadExport($export, $fileName),
            $this->isQueued() => $this->queueExport($export, $fileName),
            $this->isStored() => $this->storeExport($export, $fileName),
            default => $this->defaultExport($export, $fileName),
        };
    }

    /**
     * Get the type of the operation.
     */
    public function type(): string
    {
        return 'bulk';
    }

    /**
     * Download the export file.
     *
     * @param  ExportsTable  $export
     * @param  string  $fileName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    protected function downloadExport($export, $fileName)
    {
        return Excel::download($export, $fileName, $this->getFileType());
    }

    /**
     * Queue the export for background processing.
     *
     * @param  ExportsTable  $export
     * @param  string  $fileName
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    protected function queueExport($export, $fileName)
    {
        return Excel::queue(
            $export, $fileName, $this->getDisk(), $this->getFileType()
        )->onQueue($this->getQueue());
    }

    /**
     * Store the export file.
     *
     * @param  ExportsTable  $export
     * @param  string  $fileName
     * @return bool
     */
    protected function storeExport($export, $fileName)
    {
        return Excel::store(
            $export, $fileName, $this->getDisk(), $this->getFileType()
        );
    }

    /**
     * Default the export to be stored on disk if no other method is specified.
     *
     * @param  ExportsTable  $export
     * @param  string  $fileName
     * @return bool
     */
    protected function defaultExport($export, $fileName)
    {
        return $this->storeExport($export, $fileName);
    }

    /**
     * Use a callback to create the export.
     *
     * @param  (Closure(mixed...):mixed)  $use
     * @return mixed
     */
    protected function useCallback($use, Table $table)
    {
        $exporter = $this->getExporter($table);

        $export = new $exporter($table, $this->getEvents());

        return $this->evaluate($use, [
            'table' => $table,
            'export' => $export,
        ], [
            $table::class => $table,
            Table::class => $table,
            $export::class => $export,
            Exporter::class => $export,
        ]);
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
