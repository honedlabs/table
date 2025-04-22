<?php

declare(strict_types=1);

namespace Honed\Table\Http\Controllers;

use Honed\Table\Table;
use Honed\Action\Http\Requests\DispatchableRequest;
use Honed\Action\Http\Requests\InvokableRequest;
use Illuminate\Routing\Controller;

class TableController extends Controller
{
    /**
     * Find and execute the appropriate action from route binding.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
     *
     * @param  \Honed\Action\Table<TModel, TBuilder>  $action
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function invoke(InvokableRequest $request, Table $action)
    {
        return $action->handle($request);
    }

    /**
     * Find and execute the appropriate action from the request input.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function dispatch(DispatchableRequest $request)
    {
        /** @var string */
        $key = $request->validated('id');

        /** @var \Honed\Action\Contracts\Handles|null */
        $action = $this->baseClass()::tryFrom($key);

        abort_unless((bool) $action, 404);

        return $action->handle($request);
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
