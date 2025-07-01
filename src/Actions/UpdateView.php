<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Table\Facades\Views;
use Honed\Table\Table;
use Illuminate\Http\Request;

class UpdateView extends ViewAction
{
    /**
     * Update, or create, a view.
     */
    public function handle(
        Table $table,
        Request $request,
        ?string $name = null,
        mixed $scope = null,
        ?string $field = null
    ): void {
        if ($table->isNotViewable()) {
            $this->invalid($field, 'update');
        }

        $name ??= $this->getName($request, $field);

        $view = $this->state($table, $request);

        $this->update($table, $scope, $name, $view);
    }

    /**
     * Update a view.
     *
     * @param  array<string, mixed>  $view
     */
    protected function update(Table $table, mixed $scope, string $name, array $view): void
    {
        Views::for($scope)->set($table, $name, $view);
    }
}
