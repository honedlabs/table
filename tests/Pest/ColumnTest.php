<?php

declare(strict_types=1);

use Honed\Refine\Sort;
use Carbon\Carbon;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Columns\HiddenColumn;
use Honed\Table\Columns\NumberColumn;
use Honed\Table\Columns\BooleanColumn;

beforeEach(function () {
    $this->param = 'name';
    $this->test = Column::make($this->param);
});

it('applies', function () {
    expect($this->test->apply('value'))->toBe('value');

    expect($this->test->transformer(fn ($value) => $value * 2))
        ->toBeInstanceOf(Column::class)
        ->and($this->test->apply(2))->toBe(4);
});

it('has array representation', function () {
    expect($this->test->toArray())->toEqual([
        'name' => $this->param,
        'label' => ucfirst($this->param),
        'type' => 'column',
        'hidden' => false,
        'icon' => null,
        'toggleable' => true,
        'active' => true,
        'sort' => null,
        'meta' => [],
        'class' => null,
    ]);
});

it('is sortable', function () {
    expect($this->test)
        ->isSortable()->toBeFalse()
        ->sortable()->toBe($this->test)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getDirection()->toBeNull()
            ->getNextDirection()->toBe($this->param)
        )
        ->toArray()->{'sort'}->toBe([
            'active' => false,
            'direction' => null,
            'next' => $this->param,
        ]);
});

it('is sortable on sort alias', function () {
    expect($this->test)
        ->sortable('description', 'alias')->toBe($this->test)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getDirection()->toBeNull()
            ->getNextDirection()->toBe('alias')
        );
});

it('is sortable using column alias', function () {
    expect($this->test)
        ->sortable('description', 'alias')->toBe($this->test)
        ->alias('alias')->toBe($this->test)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getDirection()->toBeNull()
            ->getNextDirection()->toBe('alias')
        );
});

it('can disable sortable', function () {
    expect($this->test)
        ->sortable()->toBe($this->test)
        ->isSortable()->toBeTrue()
        ->sortable(false)->toBe($this->test)
        ->isSortable()->toBeFalse();
});

it('is sometimes toggleable', function () {
    expect($this->test)
        ->isToggleable()->toBeTrue()
        ->isSometimes()->toBeFalse()
        ->sometimes()->toBe($this->test)
        ->isSometimes()->toBeTrue()
        ->isToggleable()->toBeTrue()
        ->toArray()->scoped(fn ($array) => $array
            ->{'toggleable'}->toBe(true)
            ->{'active'}->toBe(true)
        );
});

it('is always toggleable', function () {
    expect($this->test)
        ->isToggleable()->toBeTrue()
        ->isAlways()->toBeFalse()
        ->always()->toBe($this->test)
        ->isAlways()->toBeTrue()
        ->isToggleable()->toBeFalse()
        ->toArray()->scoped(fn ($array) => $array
            ->{'toggleable'}->toBe(false)
            ->{'active'}->toBe(true)
        );
});

it('is searchable', function () {
    expect($this->test)
        ->isSearchable()->toBeFalse()
        ->searchable()->toBe($this->test)
        ->isSearchable()->toBeTrue();
});

it('has classes', function () {
    expect($this->test)
        ->hasClass()->toBeFalse()
        ->getClass()->toBeNull()
        ->class('bg-red-500 text-white', 'font-bold')->toBe($this->test)
        ->hasClass()->toBeTrue()
        ->getClass()->toBe('bg-red-500 text-white font-bold');
});

it('creates record', function () {
    $product = product();

    expect($this->test->createRecord($product))->toBe([
        $this->param => $product->name,
    ]);
});

it('creates record using', function () {
    $product = product();

    expect($this->test)
        ->getUsing()->toBeNull()
        ->using(fn ($product) => $product->price())->toBe($this->test)
        ->getUsing()->toBeInstanceOf(\Closure::class)
        ->createRecord($product)->toBe([
            $this->param => '$10.00',
        ]);
});


it('can be a boolean column', function () {
    $column = BooleanColumn::make($this->param);

    expect($column)
        ->toBe($column)
        ->getType()->toBe('boolean')
        ->getTrueLabel()->toBe('True')
        ->getFalseLabel()->toBe('False')
        ->trueLabel('Yes')->toBe($column)
        ->getTrueLabel()->toBe('Yes')
        ->falseLabel('No')->toBe($column)
        ->getFalseLabel()->toBe('No')
        ->labels('Enabled', 'Disabled')->toBe($column)
        ->formatValue(true)->toBe('Enabled')
        ->formatValue(false)->toBe('Disabled');
});

it('can be a date column', function () {
    $column = DateColumn::make($this->param);

    expect($column)
        ->toBe($column)
        ->getType()->toBe('date')
        ->getFormat()->toBeNull()
        ->format('d M Y')->toBe($column)
        ->getFormat()->toBe('d M Y')
        ->formatValue('2021-01-01')->toBe('01 Jan 2021')
        ->isDiff()->toBeFalse()
        ->diff()->toBe($column)
        ->isDiff()->toBeTrue()
        ->apply(Carbon::parse('1999-12-31'))->toBe('1 day ago')
        ->timezone('America/New_York')->toBe($column)
        ->getTimezone()->toBe('America/New_York')
        ->fallback('-')->toBe($column)
        ->apply(null)->toBe('-')
        ->apply('invalid date')->toBe('-');
});

it('can be a hidden column', function () {
    $column = HiddenColumn::make($this->param);

    expect($column)
        ->toBe($column)
        ->getType()->toBe('hidden')
        ->isAlways()->toBeTrue()
        ->isHidden()->toBeTrue();
});

it('can be a key column', function () {
    $column = KeyColumn::make($this->param);

    expect($column)
        ->toBe($column)
        ->getType()->toBe('key')
        ->isKey()->toBeTrue()
        ->isHidden()->toBeTrue();
});

it('can be a number column', function () {
    $column = NumberColumn::make($this->param);

    expect($column)
        ->toBe($column)
        ->getType()->toBe('number')
        ->isAbbreviated()->toBeFalse()
        ->abbreviate()->toBe($column)
        ->isAbbreviated()->toBeTrue()
        ->formatValue(1000)->toBe('1K')
        ->abbreviate(false)->toBe($column)
        ->decimals(2)->toBe($column)
        ->getDecimals()->toBe(2)
        ->formatValue(1.23456789)->toBe('1.23');
});

it('can be a text column', function () {
    $column = TextColumn::make($this->param);

    expect($column)
        ->toBe($column)
        ->getType()->toBe('text')
        ->getPrefix()->toBeNull()
        ->getSuffix()->toBeNull()
        ->prefix('#')->toBe($column)
        ->getPrefix()->toBe('#')
        ->suffix('!')->toBe($column)
        ->getSuffix()->toBe('!')
        ->length(10)->toBe($column)
        ->getLength()->toBe(10)
        ->formatValue('1234567890')->toBe('#123456789')
        ->fallback('-')->toBe($column)
        ->apply(null)->toBe('-');
});