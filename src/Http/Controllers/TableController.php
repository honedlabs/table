<?php

declare(strict_types=1);

namespace Honed\Table\Http\Controllers;

use Honed\Action\Http\Controllers\Controller;
use Honed\Action\Http\Requests\InvokableRequest;
use Honed\Table\Table;

class TableController extends Controller
{
    /**
     * Find and execute the appropriate action from route binding.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Honed\Action\Exceptions\InvalidOperationException
     * @throws \Honed\Action\Exceptions\OperationNotFoundException
     */
    public function invoke(InvokableRequest $request, Table $table)
    {
        return $table->handle($request);
    }

    /**
     * Get the class containing the action handler.
     *
     * @return class-string<\Honed\Action\Contracts\HandlesOperations>
     */
    protected function from()
    {
        return Table::class;
    }
}
