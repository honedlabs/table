<?php

declare(strict_types=1);

namespace Honed\Table\Exporters;

use Maatwebsite\Excel\Concerns\FromArray;

class ArrayExporter extends Exporter implements FromArray
{
    /**
     * Get the source of the export.
     *
     * @return array<int, array<string, mixed>>
     */
    public function array(): array
    {
        return [];
    }
}
