<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Tables'));
});

it('makes tables', function () {
    $this->artisan('make:table', [
        'name' => 'TestTable',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Tables/TestTable.php'));
});

it('prompts for a table name', function () {
    $this->artisan('make:table', [
        '--force' => true,
    ])->expectsQuestion('What should the table be named?', 'UserTable')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Tables/UserTable.php'));
});

it('makes columns', function () {
    $this->artisan('make:column', [
        'name' => 'TestColumn',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Tables/Columns/TestColumn.php'));
});

it('prompts for a column name', function () {
    $this->artisan('make:column', [
        '--force' => true,
    ])->expectsQuestion('What should the column be named?', 'UserColumn')
        ->assertSuccessful();
});

