<?php

declare(strict_types=1);

use Honed\Table\Table;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = Table::make();
});

it('is toggleable', function () {
    expect($this->table)
        ->isToggleable()->toBeFalse()
        ->toggleable()->toBe($this->table)
        ->isToggleable()->toBeTrue();
});

it('has column key', function () {
    expect($this->table)
        ->getColumnKey()->toBe(Table::COLUMN_KEY)
        ->columnKey('name')->toBe($this->table)
        ->getColumnKey()->toBe('name');
});

it('is toggleable via contract', function () {
    expect(ProductTable::make())
        ->isToggleable()->toBeTrue();
});
