<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Table;

beforeEach(function () {
    $this->table = Table::make();
});

it('adds columns', function () {
    expect($this->table)
        ->columns(Column::make('id'))->toBe($this->table)
        ->columns([Column::make('public_id')])->toBe($this->table)
        ->getColumns()->toHaveCount(2);
});

it('inserts a column', function () {
    expect($this->table)
        ->column(Column::make('id'))->toBe($this->table)
        ->getColumns()->toHaveCount(1);
});

it('retrieves with authorization', function () {
    expect($this->table)
        ->columns(Column::make('id')->allow(false))->toBe($this->table)
        ->getColumns()->toBeEmpty();
});

it('sets columns', function () {
    expect($this->table)
        ->getColumns()->toBeEmpty()
        ->setColumns([Column::make('id')])->toBeNull()
        ->getColumns()->toHaveCount(1);
});

it('sets headings', function () {
    expect($this->table)
        ->columns(Column::make('id'))->toBe($this->table)
        ->getHeadings()->toHaveCount(1)
        ->setHeadings([Column::make('public_id'), Column::make('name')])->toBeNull()
        ->getHeadings()->toHaveCount(2);
});

it('gets active columns', function () {
    expect($this->table)
        ->columns(Column::make('id'))->toBe($this->table)
        ->getActiveColumns()->toHaveCount(1)
        ->columns(Column::make('name')->allow(false))->toBe($this->table)
        ->getActiveColumns()->toHaveCount(1)
        ->column(Column::make('description')->notActive())->toBe($this->table)
        ->getActiveColumns()->toHaveCount(1);
});

it('has array representation', function () {
    expect($this->table)
        ->columnsToArray()->toBeEmpty()
        ->columns(Column::make('id'))->toBe($this->table)
        ->columnsToArray()->toHaveCount(1);
});
