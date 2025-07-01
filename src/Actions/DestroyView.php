<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Table\Facades\Views;
use Honed\Table\Table;
use Illuminate\Http\Request;

class DestroyView extends ViewAction
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
            $this->invalid($field, 'delete');
        }

        $name ??= $this->getName($request, $field);

        $this->destroy($table, $scope, $name);
    }

    /**
     * Delete a view.
     */
    protected function destroy(Table $table, mixed $scope, string $name): void
    {
        Views::for($scope)->delete($table, $name);
    }
}
