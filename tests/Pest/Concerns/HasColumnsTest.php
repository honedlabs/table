<?php

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->table = exampleTable();
    $this->blank = blankTable();
});

it('can determine if the table has no columns', function () {
    expect($this->blank->hasColumns())->toBeFalse();
    expect($this->table->hasColumns())->toBeTrue();
});

it('can set columns', function () {
    $this->blank->setColumns([
        Column::make('test'),
    ]);

    expect($this->blank->getColumns())
        ->toHaveCount(1)
        ->every(fn ($column) => $column instanceof Column)->toBeTrue();
});

it('rejects null columns', function () {
    $this->table->setColumns(null);

    expect($this->table->getColumns())->not->toBeEmpty();
});

it('can get columns', function () {
    expect($this->table->getColumns())->toBeCollection()
        ->not->toBeEmpty();

    expect($this->blank->getColumns())->toBeCollection()
        ->toBeEmpty();
});

it('can get inline columns', function () {
    expect($this->table->getSortableColumns())->toBeCollection()
        ->not->toBeEmpty()
        ->toHaveCount(1);
});

it('can get searchable columns', function () {
    expect($this->table->getSearchableColumns())->toBeCollection()
        ->not->toBeEmpty()
        ->toHaveCount(1);
});

it('can get key column', function () {
    expect($this->table->getKeyColumn())->toBeInstanceOf(Column::class)
        ->getName()->toBe('id');
});
