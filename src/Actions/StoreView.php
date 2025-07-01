<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Table\Facades\Views;
use Honed\Table\Table;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StoreView extends ViewAction
{
    /**
     * Store a new view.
     *
     * @throws ValidationException
     */
    public function handle(
        Table $table,
        Request $request,
        string $field = 'name',
        mixed $scope = null
    ): void {
        if ($table->isNotViewable()) {
            $this->invalid($field, 'create');
        }

        try {
            $name = $this->getName($request, $field);

            $view = $this->state($table, $request);

            $this->store($table, $scope, $name, $view);

        } catch (UniqueConstraintViolationException $e) {
            $this->notUnique($field);
        }
    }

    /**
     * Store a view.
     *
     * @param  array<string, mixed>  $view
     */
    protected function store(Table $table, mixed $scope, string $name, array $view): void
    {
        Views::for($scope)->create($table, $name, $view);
    }
}
