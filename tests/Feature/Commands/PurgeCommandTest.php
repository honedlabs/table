<?php

declare(strict_types=1);

use Honed\Table\Facades\Views;
use Illuminate\Support\Facades\DB;
use Workbench\App\Models\User;
use Workbench\App\Tables\ProductTable;
use Workbench\App\Tables\UserTable;

beforeEach(function () {
    $scope = Views::serializeScope(User::factory()->create());

    $this->userTable = Views::serializeTable(UserTable::make());
    $this->productTable = Views::serializeTable(ProductTable::make());

    DB::table('views')->insert([
        'table' => $this->userTable,
        'name' => 'Users',
        'scope' => $scope,
        'view' => \json_encode(['name' => 'test']),
    ]);

    DB::table('views')->insert([
        'table' => $this->productTable,
        'name' => 'Products',
        'scope' => $scope,
        'view' => \json_encode(['name' => 'test']),
    ]);
});

it('purges all views', function () {
    $this->artisan('views:purge', [
        '--store' => 'database',
    ])->assertSuccessful();

    $this->assertDatabaseEmpty('views');
});

it('purges by table', function () {
    $this->artisan('views:purge', [
        'tables' => [UserTable::class],
        '--store' => 'database',
    ])
        ->assertSuccessful();

    $this->assertDatabaseCount('views', 1);

    $this->assertDatabaseHas('views', [
        'table' => $this->productTable,
    ]);

    $this->assertDatabaseMissing('views', [
        'table' => $this->userTable,
    ]);
});
