<?php

declare(strict_types=1);

namespace Honed\Table\Contracts;

use Honed\Table\Table;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

/**
 * @extends WithMapping<array<string, mixed>|\Illuminate\Database\Eloquent\Model>
 */
interface ExportsTable extends WithEvents, WithHeadings, WithMapping, WithStyles
{
    /**
     * Create a new table export.
     *
     * @param  array<class-string<\Maatwebsite\Excel\Events\Event>, callable>  $events
     * @return void
     */
    public function __construct(Table $table, array $events = []);
}
