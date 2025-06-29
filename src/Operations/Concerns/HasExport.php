<?php

declare(strict_types=1);

namespace Honed\Table\Operations\Concerns;

use Closure;
use Maatwebsite\Excel\Excel;

trait HasExport
{
    /**
     * The name of the file to be generated.
     *
     * @var string|Closure(mixed...):string|null
     */
    protected $fileName;

    /**
     * The type of the export to be generated.
     *
     * @var string
     */
    protected $fileType = Excel::XLSX;

    /**
     * Whether the export is downloaded.
     *
     * @var bool
     */
    protected $download = false;

    /**
     * Whether the export is stored on disk.
     *
     * @var bool
     */
    protected $store = false;

    /**
     * The queue to be used for the export.
     *
     * @var bool|string
     */
    protected $queue = false;

    /**
     * The disk for the export to be stored on.
     *
     * @var string|null
     */
    protected $disk;

    /**
     * Set the name of the file to be generated.
     *
     * @param  string|Closure(mixed...):string|null  $value
     * @return $this
     */
    public function fileName($value = null)
    {
        $this->fileName = $value;

        return $this;
    }

    /**
     * Get the name of the file to be generated.
     *
     * @return string
     */
    public function getFileName()
    {
        /** @var string|null */
        $fileName = $this->evaluate($this->fileName);

        $extension = mb_strtolower($this->getFileType());

        return match (true) {
            ! $fileName => 'export.'.$extension,
            ! str_contains($fileName, '.') => $fileName.'.'.$extension,
            default => $fileName,
        };
    }

    /**
     * Set the type of the export to be generated.
     *
     * @param  string  $value
     * @return $this
     */
    public function fileType($value)
    {
        $this->fileType = $value;

        return $this;
    }

    /**
     * Get the type of the export to be generated.
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Set the export to be downloaded.
     *
     * @param  bool  $value
     * @return $this
     */
    public function download($value = true)
    {
        $this->download = $value;

        return $this;
    }

    /**
     * Set the export to not be downloaded.
     *
     * @param  bool  $value
     * @return $this
     */
    public function dontDownload($value = true)
    {
        return $this->download(! $value);
    }

    /**
     * Determine if the export is downloaded.
     *
     * @return bool
     */
    public function isDownload()
    {
        return $this->download;
    }

    /**
     * Determine if the export is not downloaded.
     *
     * @return bool
     */
    public function isNotDownload()
    {
        return ! $this->isDownload();
    }

    /**
     * Set the export to be stored on disk.
     *
     * @param  bool  $value
     * @return $this
     */
    public function store($value = true)
    {
        $this->store = $value;

        return $this;
    }

    /**
     * Set the export to not be stored on disk.
     *
     * @param  bool  $value
     * @return $this
     */
    public function dontStore($value = true)
    {
        return $this->store(! $value);
    }

    /**
     * Determine if the export is stored on disk.
     *
     * @return bool
     */
    public function isStored()
    {
        return $this->store;
    }

    /**
     * Determine if the export is not stored on disk.
     *
     * @return bool
     */
    public function isNotStored()
    {
        return ! $this->isStored();
    }

    /**
     * Set the export to be queued.
     *
     * @param  bool|string  $value
     * @return $this
     */
    public function queue($value = true)
    {
        $this->queue = $value;

        return $this;
    }

    /**
     * Set the export to not be queued.
     *
     * @param  bool  $value
     * @return $this
     */
    public function dontQueue($value = true)
    {
        return $this->queue(! $value);
    }

    /**
     * Determine if the export is queued.
     *
     * @return bool
     */
    public function isQueued()
    {
        return (bool) $this->queue;
    }

    /**
     * Determine if the export is not queued.
     *
     * @return bool
     */
    public function isNotQueued()
    {
        return ! $this->isQueued();
    }

    /**
     * Get the queue to be used for the export.
     *
     * @return string|null
     */
    public function getQueue()
    {
        if (is_bool($this->queue)) {
            return null;
        }

        return $this->queue;
    }

    /**
     * Set the disk to be used for the export.
     *
     * @param  string|null  $value
     * @return $this
     */
    public function disk($value = null)
    {
        $this->disk = $value;

        return $this->store();
    }

    /**
     * Get the disk to be used for the export.
     *
     * @return string|null
     */
    public function getDisk()
    {
        return $this->disk;
    }
}
