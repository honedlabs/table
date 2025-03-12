<?php

declare(strict_types=1);

use Honed\Refine\Sort;
use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\HiddenColumn;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\Columns\NumberColumn;
use Honed\Table\Columns\TextColumn;

beforeEach(function () {
    $this->param = 'name';
    $this->test = Column::make($this->param);
});

it('applies to a value', function () {
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

it('can be sortable', function () {
    expect($this->test)
        ->isSortable()->toBeFalse()
        ->sortable()->toBeInstanceOf(Column::class)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getDirection()->toBeNull()
            ->getNextDirection()->toBe($this->param)
        )
        ->toArray()->toEqual([
            'name' => $this->param,
            'label' => ucfirst($this->param),
            'type' => 'column',
            'hidden' => false,
            'icon' => null,
            'toggleable' => true,
            'active' => true,
            'sort' => [
                'direction' => null,
                'next' => $this->param,
            ],
            'class' => null,
            'meta' => [],
        ])
        ->sortable(false)->toBeInstanceOf(Column::class)
        ->isSortable()->toBeFalse()
        ->getSort()->toBeNull()
        ->toArray()->toEqual([
            'name' => $this->param,
            'label' => ucfirst($this->param),
            'type' => 'column',
            'hidden' => false,
            'icon' => null,
            'toggleable' => true,
            'active' => true,
            'sort' => null,
            'class' => null,
            'meta' => [],
        ])
        ->sortable('description')->toBeInstanceOf(Column::class)
        ->getSort()->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getDirection()->toBeNull()
            ->getNextDirection()->toBe('description')
        )
        ->isSortable()->toBeTrue()
        ->toArray()->toEqual([
            'name' => 'name',
            'label' => 'Name',
            'type' => 'column',
            'hidden' => false,
            'icon' => null,
            'toggleable' => true,
            'active' => true,
            'sort' => [
                'direction' => null,
                'next' => 'description',
            ],
            'class' => null,
            'meta' => [],
        ]);
});

it('can be toggleable', function () {
    expect($this->test)
        ->isToggleable()->toBeTrue()
        ->sometimes()->toBeInstanceOf(Column::class)
        ->isSometimes()->toBeTrue()
        ->isToggleable()->toBeTrue()
        ->toArray()->toEqual([
            'name' => $this->param,
            'label' => ucfirst($this->param),
            'type' => 'column',
            'hidden' => false,
            'icon' => null,
            'toggleable' => true,
            'active' => true,
            'class' => null,
            'sort' => null,
            'meta' => [],
        ])
        ->always()->toBeInstanceOf(Column::class)
        ->isToggleable()->toBeFalse()
        ->isAlways()->toBeTrue()
        ->toArray()->toEqual([
            'name' => $this->param,
            'label' => ucfirst($this->param),
            'type' => 'column',
            'hidden' => false,
            'icon' => null,
            'toggleable' => false,
            'active' => true,
            'class' => null,
            'sort' => null,
            'meta' => [],
        ]);
});

it('can be searchable', function () {
    expect($this->test)
        ->isSearchable()->toBeFalse()
        ->searchable()->toBeInstanceOf(Column::class)
        ->isSearchable()->toBeTrue();
});

it('can have classes', function () {
    expect($this->test)
        ->hasClass()->toBeFalse()
        ->getClass()->toBeNull()
        ->class('bg-red-500 text-white', 'font-bold')->toBeInstanceOf(Column::class)
        ->hasClass()->toBeTrue()
        ->getClass()->toBe('bg-red-500 text-white font-bold');
});

it('has other column types', function () {
    expect(HiddenColumn::make($this->param))
        ->toBeInstanceOf(Column::class)
        ->getType()->toBe('hidden')
        ->isAlways()->toBeTrue()
        ->isHidden()->toBeTrue();

    expect(KeyColumn::make($this->param))
        ->toBeInstanceOf(Column::class)
        ->getType()->toBe('key')
        ->isKey()->toBeTrue()
        ->isHidden()->toBeTrue();
});
