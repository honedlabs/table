<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('has column classes', function () {
    expect($this->column)
        ->getColumnClass()->toBeNull()
        ->columnClass('bg-red-500')->toBe($this->column)
        ->getColumnClass()->toBe('bg-red-500')
        ->columnClass('text-white')->toBe($this->column)
        ->getColumnClass()->toBe('bg-red-500 text-white');
});

it('has column classes with closure', function () {
    $this->column->columnClass(fn () => 'bg-blue-500');

    expect($this->column)
        ->getColumnClass()->toBe('bg-blue-500');
});

it('has heading column class', function () {
    expect($this->column)
        ->getColumnClass()->toBeNull()
        ->columnClass('font-bold')->toBe($this->column)
        ->getColumnClass()->toBe('font-bold')
        ->columnClass('text-gray-900')->toBe($this->column)
        ->getColumnClass()->toBe('font-bold text-gray-900');
});

it('has heading column class with closure', function () {
    $this->column->columnClass(fn () => 'text-center');

    expect($this->column)
        ->getColumnClass()->toBe('text-center');
});

it('can mix different types of columnClass', function () {
    $this->column
        ->classes('font-bold')
        ->columnClass('bg-blue-100');

    expect($this->column)
        ->getClasses()->toBe('font-bold')
        ->getColumnClass()->toBe('bg-blue-100');
});
