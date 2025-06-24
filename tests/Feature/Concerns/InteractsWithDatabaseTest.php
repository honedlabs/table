<?php

declare(strict_types=1);

use Honed\Table\Migrations\ViewMigration;

beforeEach(function () {
    $this->migration = new class() extends ViewMigration {};
});

it('has connection', function () {
    expect($this->migration)
        ->getConnection()->toBe(config('database.default'));
});

it('has table name', function () {
    expect($this->migration)
        ->getTableName()->toBe('views');
});
