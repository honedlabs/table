<?php

declare(strict_types=1);

use Honed\Table\Actions\ViewAction;
use Honed\Table\Facades\Views;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;
use Workbench\App\Tables\UserTable;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);

    $this->name = 'View';

    $this->table = ProductTable::make();

    Views::for()->create(
        $this->table, $this->name, []
    );
});

it('destroys a view', function () {
    assertDatabaseCount(config('table.views.table'), 1);

    put(route('table.views.destroy', $this->table), [
        ViewAction::FIELD => $this->name,
    ])->assertRedirect();

    assertDatabaseCount(config('table.views.table'), 0);
});

it('throws exception when the table is not viewable', function () {
    $table = UserTable::make();

    put(route('table.views.store', $table), [
        ViewAction::FIELD => 'View',
    ])->assertSessionHasErrors(ViewAction::FIELD);
});

it('does not destroy a view which does not match the request', function () {
    Views::for()->create(
        $this->table, 'Duplicate', []
    );

    assertDatabaseCount(config('table.views.table'), 2);

    put(route('table.views.destroy', $this->table), [
        ViewAction::FIELD => $this->name,
    ])->assertRedirect();

    assertDatabaseCount(config('table.views.table'), 1);
});
