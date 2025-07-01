<?php

declare(strict_types=1);

use Honed\Table\Actions\DestroyView;
use Honed\Table\Facades\Views;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Workbench\App\Tables\ProductTable;
use Workbench\App\Tables\UserTable;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

beforeEach(function () {
    $this->action = new DestroyView();

    $this->table = ProductTable::make();

    $query = $this->table->getSearchKey().'=term';

    $this->name = 'View';

    $this->request = Request::create('/?'.$query, Request::METHOD_GET, [
        DestroyView::FIELD => $this->name,
    ]);

    Views::for()->create(
        $this->table, $this->name, []
    );
});

it('destroys a view', function () {

    assertDatabaseCount(config('table.views.table'), 1);

    $this->action->handle($this->table, $this->request);

    assertDatabaseEmpty(config('table.views.table'));
});

it('throws exception when the table is not viewable', function () {
    $table = UserTable::make();

    $this->action->handle($table, $this->request);
})->throws(ValidationException::class);

it('does not destroy a view which does not match the request', function () {
    Views::for()->create(
        $this->table, 'Duplicate', []
    );

    assertDatabaseCount(config('table.views.table'), 2);

    $this->action->handle($this->table, $this->request);

    assertDatabaseCount(config('table.views.table'), 1);
});
