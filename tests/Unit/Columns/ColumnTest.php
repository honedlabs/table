<?php

use Honed\Table\Columns\Column;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('has an array form', function () {
    expect($this->column->toArray())->toEqual([
        'name' => 'name',
        'label' => 'Name',
        'type' => 'default',
        'tooltip' => null,
        'breakpoint' => null,
        'isHidden' => false,
        'isScreenReader' => false,
        'isToggleable' => false,
        'isActive' => false,
        'isSortable' => false,
        'isSorting' => false,
        'direction' => null,
        'meta' => [],
    ]);
});

it('can be made', function () {
    expect(Column::make('created_at'))->toBeInstanceOf(Column::class)
        ->toArray()->toEqual([
            'name' => 'created_at',
            'label' => 'Created At',
            'type' => 'default',
            'tooltip' => null,
            'breakpoint' => null,
            'isHidden' => false,
            'isScreenReader' => false,
            'isToggleable' => false,
            'isActive' => false,
            'isSortable' => false,
            'isSorting' => false,
            'direction' => null,
            'meta' => [],
        ]);
});

it('can be applied to a record', function () {
    expect($this->column->transform(fn (Product $product) => 'none')
        ->apply(Product::find(1)))->toBe('none');
});

it('can format a record', function () {
    expect($this->column->placeholder('test')
        ->formatValue(null)->apply(Product::find(1)))->toBe('test');
});
