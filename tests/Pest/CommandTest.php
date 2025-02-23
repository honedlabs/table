<?php

declare(strict_types=1);

it('tests', function () {
    $this->artisan('make:table', [
        'name' => 'TestTable',
    ])->assertSuccessful();

    $this->assertFileExists(app_path('Tables/TestTable.php'));
});
