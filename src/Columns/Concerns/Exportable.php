<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Closure;

trait Exportable
{
    /**
     * How the column should be exported.
     *
     * @var bool|Closure(mixed...):mixed
     */
    protected $exportable = true;

    /**
     * The format to export the column as.
     *
     * @var string|null
     */
    protected $exportFormat;

    /**
     * The style to export the column as.
     *
     * @var array<string,mixed>|(Closure(\PhpOffice\PhpSpreadsheet\Style\Style):void)|null
     */
    protected $exportStyle;

    /**
     * Set whether the column should be exported.
     *
     * @param  bool|Closure(mixed...):mixed  $value
     * @return $this
     */
    public function exportable($value = true)
    {
        $this->exportable = $value;

        return $this;
    }

    /**
     * Check if the column is exportable.
     *
     * @return bool
     */
    public function isExportable()
    {
        return (bool) $this->exportable;
    }

    /**
     * Get the exportable value.
     *
     * @return bool|Closure(mixed...):mixed
     */
    public function getExportable()
    {
        return $this->exportable;
    }

    /**
     * Set the style to export the column as.
     *
     * @param  array<string,mixed>|(Closure(\PhpOffice\PhpSpreadsheet\Style\Style):void)  $style
     * @return $this
     */
    public function exportStyle($style)
    {
        $this->exportStyle = $style;

        return $this;
    }

    /**
     * Get the style to export the column as.
     *
     * @return array<string,mixed>|(Closure(\PhpOffice\PhpSpreadsheet\Style\Style):void)|null
     */
    public function getExportStyle()
    {
        return $this->exportStyle;
    }

    /**
     * Set the format to export the column as.
     *
     * @param  string  $format
     * @return $this
     */
    public function exportFormat($format)
    {
        $this->exportFormat = $format;

        return $this;
    }

    /**
     * Get the format to export the column as.
     *
     * @return string|null
     */
    public function getExportFormat()
    {
        return $this->exportFormat;
    }
}
