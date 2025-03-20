<?php

declare(strict_types=1);

use Honed\Table\TableServiceProvider;

it('publishes config', function () {
    $this->artisan('vendor:publish', ['--provider' => TableServiceProvider::class])
        ->assertSuccessful();

    expect(file_exists(base_path('config/table.php')))->toBeTrue();
    expect(file_exists(base_path('stubs/honed.table.stub')))->toBeTrue();
    expect(file_exists(base_path('stubs/honed.column.stub')))->toBeTrue();
});


