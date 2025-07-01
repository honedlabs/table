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
    public function exportable(bool|Closure $value = true): static
    {
        $this->exportable = $value;

        return $this;
    }

    /**
     * Set the instance to not be exportable.
     *
     * @return $this
     */
    public function notExportable(bool $value = true): static
    {
        return $this->exportable(! $value);
    }

    /**
     * Check if the column is exportable.
     */
    public function isExportable(): bool
    {
        return (bool) $this->exportable;
    }

    /**
     * Determine if the column is not exportable.
     */
    public function isNotExportable(): bool
    {
        return ! $this->isExportable();
    }

    /**
     * Get the exportable value.
     *
     * @return bool|Closure(mixed...):mixed
     */
    public function getExportable(): bool|Closure
    {
        return $this->exportable;
    }

    /**
     * Set the style to export the column as.
     *
     * @param  array<string,mixed>|(Closure(\PhpOffice\PhpSpreadsheet\Style\Style):void)  $style
     * @return $this
     */
    public function exportStyle(array|Closure $style): static
    {
        $this->exportStyle = $style;

        return $this;
    }

    /**
     * Get the style to export the column as.
     *
     * @return array<string,mixed>|(Closure(\PhpOffice\PhpSpreadsheet\Style\Style):void)|null
     */
    public function getExportStyle(): array|Closure|null
    {
        return $this->exportStyle;
    }

    /**
     * Set the format to export the column as.
     *
     * @return $this
     */
    public function exportFormat(string $format): static
    {
        $this->exportFormat = $format;

        return $this;
    }

    /**
     * Get the format to export the column as.
     */
    public function getExportFormat(): ?string
    {
        return $this->exportFormat;
    }
}
