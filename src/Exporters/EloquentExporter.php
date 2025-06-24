<?php

declare(strict_types=1);

namespace Honed\Table\Exporters;

use Maatwebsite\Excel\Concerns\FromQuery;

class EloquentExporter extends Exporter implements FromQuery
{
    /**
     * Get the source of the export.
     *
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function query()
    {
        return $this->table->getBuilder();
    }
}
