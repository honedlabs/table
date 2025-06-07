<?php

namespace Honed\Table\Actions;

use Honed\Action\Action;
use Honed\Table\Contracts\ExportsTable;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;

class Export extends Action
{
    /**
     * The callback to be used to create the export from the table.
     * 
     * @var \Closure(\Honed\Table\Table, \Honed\Table\Contracts\ExportsTable, \Illuminate\Http\Request):mixed
     */
    protected $using;

    /**
     * The exporter to be used for the export.
     * 
     * @var \Honed\Table\Contracts\ExportsTable|null
     */
    protected $exporter;

    /**
     * The registered callback to be called if the export is not downloaded.
     * 
     * @var callable
     */
    protected $after;

    /**
     * The events that this export should listen to.
     * 
     * @var array<class-string<\Maatwebsite\Excel\Events\Event>, callable>
     */
    protected $events = [];

    /**
     * The method to be used for generating the export.
     * 
     * @var 'download'|'queue'|'store'
     */
    protected $method = 'download';

    /**
     * The default method to be used for generating the export.
     * 
     * @var 'download'|'queue'|'store'
     */
    protected static $useMethod = 'download';

    /**
     * The queue to be used for the export.
     * 
     * @var bool|string|null
     */
    protected $queue;

    /**
     * The default queue to be used for the export.
     * 
     * @var string
     */
    protected static $useQueue = 'default';

    /**
     * The disk for the export to be stored on.
     * 
     * @var string|null
     */
    protected $disk;

    /**
     * The default disk for the export to be stored on, null will use the 
     * default disk supplied by the filesystem config.
     * 
     * @var string|null
     */
    protected static $useDisk = null;

    /**
     * Whether to only use records that have been filtered.
     * 
     * @var bool|null
     */
    protected $filtered;

    /**
     * Whether to only use records that have been filtered by default.
     * 
     * @var bool|null
     */
    protected static $useFiltered = true;

    /**
     * Whether to only use records that have been selected.
     * 
     * @var bool|null
     */
    protected $selected;

    /**
     * Whether to only use records that have been selected by default.
     * 
     * @var bool|null
     */
    protected static $useSelected = true;

    public function all()
    {

    }

    /**
     * Register the callback to be used to create the export from the table.
     * 
     * @param  (\Closure(\Honed\Table\Table, \Honed\Table\Contracts\ExportsTable, \Illuminate\Http\Request):mixed)|null  $callback
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
     * @return \Closure(\Honed\Table\Table, \Honed\Table\Contracts\ExportsTable, \Illuminate\Http\Request):mixed
     */
    public function uses()
    {
        return $this->using;
    }

    /**
     * Set the exporter class to be used to generate the export.
     * 
     * @param  \Honed\Table\Contracts\ExportsTable|null  $exporter
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
     * @return \Honed\Table\Contracts\ExportsTable|null
     */
    public function getExporter()
    {
        return $this->exporter;
    }

    /**
     * Register a callback to be called after generating the export, assuming 
     * that the export is not downloaded.
     * 
     * @param  callable  $callback
     * @return $this
     */
    public function after($callback)
    {
        $this->after = $callback;

        return $this;
    }

    /**
     * Get the callback to be called after generating the export, assuming 
     * that the export is not downloaded.
     * 
     * @return callable|null
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * Hook into the underlying event that the export should listen to.
     * 
     * @param  \Maatwebsite\Excel\Events\Event  $event
     * @param  callable  $callback
     * @return $this
     */
    public function event($event, $callback)
    {
        $this->events[$event] = $callback;

        return $this;
    }

    /**
     * Register the events that the export should listen to.
     * 
     * @param array<class-string<\Maatwebsite\Excel\Events\Event>, callable> $events
     * @return $this
     */
    public function events($events)
    {
        $this->events = \array_merge($this->events, $events);

        return $this;
    }

    /**
     * Get the events that the export should listen to.
     * 
     * @return array<class-string<\Maatwebsite\Excel\Events\Event>, callable>
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set the export to be downloaded as the actionable response.
     * 
     * @return $this
     */
    public function download()
    {
        $this->method = 'download';

        return $this;
    }

    /**
     * Set the export to be stored on a disk.
     * 
     * @param  string|null  $disk
     * @return $this
     */
    public function store($disk = null)
    {
        $this->method = 'store';

        if ($disk) {
            $this->disk = $disk;
        }

        return $this;
    }

    /**
     * Set the export to be queued.
     * 
     * @param  string|null  $queue
     * @return $this
     */
    public function queue($queue = null)
    {
        $this->method = 'queue';

        if ($queue) {
            $this->queue = $queue;
        }

        return $this;
    }

    /**
     * Get the method to be used for generating the export.
     * 
     * @return 'download'|'queue'|'store'
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the default method to be used for generating the export.
     * 
     * @param  'download'|'queue'|'store'  $method
     * @return void
     */
    public static function useMethod($method)
    {
        static::$useMethod = $method;
    }

    /**
     * Get the queue to be used for the export.
     * 
     * @return string
     */
    public function getQueue()
    {
        return $this->queue ?? static::$useQueue;
    }

    /**
     * Set the queue to be used for the export.
     * 
     * @param  string|null  $queue
     * @return void
     */
    public static function useQueue($queue)
    {
        static::$useQueue = $queue;
    }

    /**
     * Get the disk to be used for the export.
     * 
     * @return string|null
     */
    public function getDisk()
    {
        return $this->disk ?? static::$useDisk;
    }

    /**
     * Set the disk to be used for the export.
     * 
     * @param  string|null  $disk
     * @return void
     */
    public static function useDisk($disk)
    {
        static::$useDisk = $disk;
    }

    /**
     * Set whether to only use records that have been filtered.
     * 
     * @param  bool|null  $filtered
     * @return void
     */
    public static function onlyFiltered($filtered = true)
    {
        static::$useFiltered = $filtered;
    }

    /**
     * Set whether to only use records that have been selected.
     * 
     * @param  bool|null  $selected
     * @return void
     */
    public static function onlySelected($selected = true)
    {
        static::$useSelected = $selected;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveToArray($parameters = [], $typed = [])
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'icon' => $this->getIcon(),
            'extra' => $this->getExtra(),
            'actionable' => true,
            'confirm' => $this->getConfirm()?->resolveToArray($parameters, $typed),
        ];
    }

    /**
     * 
     */
    public function execute($record)
    {
        /** @var \Honed\Table\Contracts\ExportsTable */
        $export = App::make(ExportsTable::class);

        $filename = $this->getFileName();

        $type = $this->getType();

        return match (true) {
            $this->downloads() => Excel::download($export, $filename, $type),
            $this->queues() => Excel::queue($export, $filename, $type)->onQueue($this->getQueue()),
            default => Excel::store($export, $filename, $this->getDisk()),
        };
    }
    

    /**
     * Statics:
     * onlyFilteredRecords
     * onlySelectedRecords
     * onlyShownColumns
     * useQueue
     * useDisk
     * 
     * Methods
     * events
     * queue
     * download
     * exporter
     * using
     * 
     * Column methods
     * export
     * exportFormat
     * dontExport
     * exportStyle
     * 
     * We feed in the table to this class.
     */

}