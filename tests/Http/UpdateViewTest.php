<?php

declare(strict_types=1);

use Honed\Table\Actions\ViewAction;
use Honed\Table\Facades\Views;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;
use Workbench\App\Tables\UserTable;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\patch;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);

    $this->name = 'View';

    $this->table = ProductTable::make();
});

it('updates a view', function () {
    Views::for()->create(
        $this->table, $this->name, []
    );

    assertDatabaseCount(config('table.views.table'), 1);

    patch(route('table.views.update', $this->table), [
        ViewAction::FIELD => $this->name,
    ])->assertRedirect();

    assertDatabaseCount(config('table.views.table'), 1);
});

it('creates a new view', function () {
    assertDatabaseEmpty(config('table.views.table'));

    patch(route('table.views.update', $this->table), [
        ViewAction::FIELD => $this->name,
    ])->assertRedirect();

    assertDatabaseCount(config('table.views.table'), 1);
});

it('throws exception when the table is not viewable', function () {
    $table = UserTable::make();

    patch(route('table.views.store', $table), [
        ViewAction::FIELD => 'View',
    ])->assertSessionHasErrors(ViewAction::FIELD);
});
