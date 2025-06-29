<?php

declare(strict_types=1);

use Honed\Table\Table;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = Table::make();
});

it('is orderable', function () {
    expect($this->table)
        ->isNotOrderable()->toBeTrue()
        ->isOrderable()->toBeFalse()
        ->orderable()->toBe($this->table)
        ->isOrderable()->toBeTrue()
        ->notOrderable()->toBe($this->table)
        ->isNotOrderable()->toBeTrue();
});

it('is orderable via contract', function () {
    expect(ProductTable::make())
        ->isOrderable()->toBeTrue();
});
