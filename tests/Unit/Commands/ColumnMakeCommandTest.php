<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    File::cleanDirectory(app_path('Columns'));
});

it('makes', function () {
    $this->artisan('make:column', [
        'name' => 'UserColumn',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Columns/UserColumn.php'));
});

it('bindings for a name', function () {
    $this->artisan('make:column', [
        '--force' => true,
    ])->expectsQuestion('What should the column be named?', 'UserColumn')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Columns/UserColumn.php'));
});
