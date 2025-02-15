<?php

declare(strict_types=1);

namespace Honed\Table\Http\Controllers;

use Honed\Table\Concerns\HasTableBindings;
use Honed\Table\Http\Requests\TableRequest;
use Illuminate\Routing\Controller;

class TableController extends Controller
{
    const TableKey = 'table';

    use HasTableBindings;

    /**
     * Delegate the incoming action request to the appropriate table.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    public function handle(TableRequest $request)
    {
        $table = $this->resolveRouteBinding(
            $request->validated(self::TableKey)
        );

        abort_unless((bool) $table, 404);

        return $table->handle($request);
    }
}
