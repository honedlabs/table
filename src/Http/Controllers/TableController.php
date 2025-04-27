<?php

declare(strict_types=1);

namespace Honed\Table\Http\Controllers;

use Honed\Table\Table;
use Illuminate\Routing\Controller;
use Honed\Action\Http\Requests\InvokableRequest;
use Honed\Action\Http\Requests\DispatchableRequest;
use Honed\Action\Exceptions\CouldNotResolveHandlerException;

class TableController extends Controller
{
    /**
     * Find and execute the appropriate action from route binding.
     *
     * @param  \Honed\Table\Table  $table
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function invoke(InvokableRequest $request, Table $table)
    {
        return $table->handle($request);
    }

    /**
     * Find and execute the appropriate action from the request input.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     * 
     * @throws \Honed\Action\Exceptions\CouldNotResolveHandlerException
     * @throws \Honed\Action\Exceptions\ActionNotFoundException
     * @throws \Honed\Action\Exceptions\ActionNotAllowedException
     * @throws \Honed\Action\Exceptions\InvalidActionException
     */
    public function dispatch(DispatchableRequest $request)
    {
        /** @var string */
        $key = $request->validated('id');

        /** @var \Honed\Action\Contracts\Handles|null */
        $table = $this->baseClass()::tryFrom($key);

        if (! $table) {
            CouldNotResolveHandlerException::throw();
        }

        return $table->handle($request);
    }

    /**
     * Get the class to use to handle the actions.
     *
     * @return class-string<\Honed\Action\Contracts\Handles>
     */
    public function baseClass()
    {
        return Table::class;
    }
}
