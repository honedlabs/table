<?php

namespace Honed\Table\Contracts;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

interface ExportsTable extends FromQuery, WithEvents, WithHeadings, WithMapping
{
    /**
     * Set the columns to be used for the export.
     *
     * @param  array<int, \Honed\Table\Columns\Column>  $columns
     * @return $this
     */
    public function columns($columns);

    /**
     * Register the events the export should listen for.
     *
     * @return $this
     */
    public function events();
}
