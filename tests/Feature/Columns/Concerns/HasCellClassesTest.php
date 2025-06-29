<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('has cell classes', function () {
    expect($this->column)
        ->getCellClasses()->toBeNull()
        ->cells('bg-red-500')->toBe($this->column)
        ->getCellClasses()->toBe('bg-red-500')
        ->cells('text-white')->toBe($this->column)
        ->getCellClasses()->toBe('bg-red-500 text-white');
});

it('has cell classes with closure', function () {
    $this->column->cells(fn () => 'bg-blue-500');

    expect($this->column)
        ->getCellClasses()->toBe('bg-blue-500');
});

it('has heading classes', function () {
    expect($this->column)
        ->getClasses()->toBeNull()
        ->classes('font-bold')->toBe($this->column)
        ->getClasses()->toBe('font-bold')
        ->classes('text-gray-900')->toBe($this->column)
        ->getClasses()->toBe('font-bold text-gray-900');
});

it('has heading classes with closure', function () {
    $this->column->classes(fn () => 'text-center');

    expect($this->column)
        ->getClasses()->toBe('text-center');
});

it('can mix different types of classes', function () {
    $this->column
        ->classes('font-bold')
        ->cells('bg-blue-100');

    expect($this->column)
        ->getClasses()->toBe('font-bold')
        ->getCellClasses()->toBe('bg-blue-100');
});
