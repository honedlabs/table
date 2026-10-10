<?php

declare(strict_types=1);

namespace Honed\Table\Exporters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Maatwebsite\Excel\Concerns\FromQuery;

class EloquentExporter extends Exporter implements FromQuery
{
    /**
     * Get the source of the export.
     *
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder<*>|\Illuminate\Database\Eloquent\Relations\Relation<*, *>
     */
    public function query(): EloquentBuilder|QueryBuilder|Relation
    {
        return $this->table->getBuilder();
    }
}
