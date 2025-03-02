<?php

declare(strict_types=1);

it('makes', function () {
    $this->artisan('make:table', [
        'name' => 'TestTable',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Tables/TestTable.php'));
});

it('prompts for a name', function () {
    $this->artisan('make:table', [
        '--force' => true,
    ])->expectsQuestion('What should the table be named?', 'UserTable')
        ->assertSuccessful();

    $this->assertFileExists(app_path('Tables/UserTable.php'));
});
