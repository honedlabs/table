<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('has cell classes', function () {
    expect($this->column)
        ->getCellClasses()->toBeNull()
        ->cellClasses('bg-red-500')->toBe($this->column)
        ->getCellClasses()->toBe('bg-red-500')
        ->cellClasses('text-white')->toBe($this->column)
        ->getCellClasses()->toBe('bg-red-500 text-white');
});

it('has cell classes with closure', function () {
    $this->column->cellClasses(fn () => 'bg-blue-500');

    expect($this->column)
        ->getCellClasses()->toBe('bg-blue-500');
});

it('has record classes', function () {
    expect($this->column)
        ->getRecordClasses()->toBeNull()
        ->recordClasses('border-l-4 border-red-500')->toBe($this->column)
        ->getRecordClasses()->toBe('border-l-4 border-red-500')
        ->recordClasses('bg-red-50')->toBe($this->column)
        ->getRecordClasses()->toBe('border-l-4 border-red-500 bg-red-50');
});

it('has record classes with closure', function () {
    $this->column->recordClasses(fn () => 'border-l-4 border-green-500');

    expect($this->column)
        ->getRecordClasses()->toBe('border-l-4 border-green-500');
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
        ->cellClasses('bg-blue-100')
        ->recordClasses('border-l-4 border-blue-500');

    expect($this->column)
        ->getClasses()->toBe('font-bold')
        ->getCellClasses()->toBe('bg-blue-100')
        ->getRecordClasses()->toBe('border-l-4 border-blue-500');
});
