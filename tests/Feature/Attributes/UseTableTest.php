<?php

declare(strict_types=1);

use Honed\Table\Attributes\UseTable;
use Workbench\App\Models\User;
use Workbench\App\Tables\UserTable;

it('has attribute', function () {
    $attribute = new UseTable(UserTable::class);

    expect($attribute)
        ->toBeInstanceOf(UseTable::class)
        ->tableClass->toBe(UserTable::class);

    expect(User::class)
        ->toHaveAttribute(UseTable::class);
});
