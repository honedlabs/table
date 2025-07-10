<?php

declare(strict_types=1);

use Honed\Table\Actions\ViewAction;
use Honed\Table\Facades\Views;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;
use Workbench\App\Tables\UserTable;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);

    $this->name = 'View';

    $this->table = ProductTable::make();

    $this->table->define(); // @TODO
});

it('stores a view', function () {
    assertDatabaseEmpty(config('table.views.table'));

    post(route('table.views.store', $this->table), [
        ViewAction::FIELD => $this->name,
    ])->assertRedirect();

    assertDatabaseHas(config('table.views.table'), [
        'table' => $this->table::class,
        'name' => $this->name,
    ]);
});

it('throws exception when the table is not viewable', function () {
    $table = UserTable::make();

    post(route('table.views.store', $table), [
        ViewAction::FIELD => 'View',
    ])->assertSessionHasErrors(ViewAction::FIELD);
});

it('throws exception when the view name is not unique', function () {
    Views::for()->create(
        $this->table, $this->name, []
    );

    post(route('table.views.store', $this->table), [
        ViewAction::FIELD => $this->name,
    ])->assertSessionHasErrors(ViewAction::FIELD);
});
