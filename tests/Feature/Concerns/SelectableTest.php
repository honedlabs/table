<?php

declare(strict_types=1);

use Honed\Table\Table;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = Table::make();
});

it('is selectable', function () {
    expect($this->table)
        ->isNotSelectable()->toBeTrue()
        ->isSelectable()->toBeFalse()
        ->getSelects()->toBe([])
        ->selectable()->toBe($this->table)
        ->isSelectable()->toBeTrue()
        ->getSelects()->toBe([])
        ->notSelectable()->toBe($this->table)
        ->isNotSelectable()->toBeTrue()
        ->getSelects()->toBe([]);
});

it('selects column', function () {
    expect($this->table)
        ->getSelects()->toBeEmpty()
        ->select('name', 'email')->toBe($this->table)
        ->getSelects()->toEqual(['name', 'email']);
});

it('selects columns', function () {
    expect($this->table)
        ->getSelects()->toBeEmpty()
        ->select(['name', 'email'])->toBe($this->table)
        ->getSelects()->toEqual(['name', 'email']);
});

it('is selectable via contract', function () {
    expect(ProductTable::make())
        ->isSelectable()->toBeTrue();
});
