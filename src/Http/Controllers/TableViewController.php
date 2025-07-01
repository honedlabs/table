<?php

declare(strict_types=1);

namespace Honed\Table\Http\Controllers;

use Honed\Table\Actions\DestroyView;
use Honed\Table\Actions\StoreView;
use Honed\Table\Actions\UpdateView;
use Honed\Table\Table;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TableViewController extends Controller
{
    /**
     * Store a newly created view in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Table $table, StoreView $action)
    {
        $action->handle($table, $request, scope: $this->scope($request));

        return back();
    }

    /**
     * Update a view in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Table $table, UpdateView $action)
    {
        $action->handle($table, $request, scope: $this->scope($request));

        return back();
    }

    /**
     * Delete a view from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Table $table, DestroyView $action)
    {
        $action->handle($table, $request, scope: $this->scope($request));

        return back();
    }

    /**
     * Define the scope to use for creating views.
     */
    protected function scope(Request $request): mixed
    {
        return null;
    }
}
