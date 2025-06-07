<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Tables'));
});

it('makes', function () {
    $this->artisan('make:table', [
        'name' => 'UserTable',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Tables/UserTable.php'));
});

it('bindings for a name', function () {
    $this->artisan('make:table', [
        '--force' => true,
    ])->expectsQuestion('What should the table be named?', 'UserTable')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Tables/UserTable.php'));
});
