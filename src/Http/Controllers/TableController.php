<?php

declare(strict_types=1);

namespace Honed\Table\Http\Controllers;

use Honed\Table\Http\Requests\TableRequest;
use Honed\Table\Table;
use Illuminate\Routing\Controller;

class TableController extends Controller
{
    const TableKey = 'table';

    /**
     * Delegate the incoming action request to the appropriate table.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function handle(TableRequest $request)
    {
        /** @var string */
        $key = $request->validated(self::TableKey);

        /**
         * @var \Honed\Table\Table<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>|null $table
         */
        $table = Table::getPrimitive($key, Table::class);

        abort_unless((bool) $table, 404);

        return $table->handle($request);
    }
}
