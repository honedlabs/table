<?php

declare(strict_types=1);

use Carbon\Carbon;
use Honed\Refine\Sort;
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

it('makes', function () {
    expect($this->test)
        ->getName()->toBe($this->param)
        ->getLabel()->toBe(ucfirst($this->param))
        ->isActive()->toBeTrue();
});

it('has parameter', function () {
    expect($this->test)
        ->getParameter()->toBe($this->param)
        ->name('relation.column')->toBe($this->test)
        ->getParameter()->toBe('relation_column')
        ->alias('alias')->toBe($this->test)
        ->getParameter()->toBe('alias');
});

it('is key', function () {
    expect($this->test)
        ->isKey()->toBeFalse()
        ->key()->toBe($this->test)
        ->isKey()->toBeTrue();
});

it('has class', function () {
    expect($this->test)
        ->getClass()->toBeNull()
        ->class('bg-red-500')->toBe($this->test)
        ->getClass()->toBe('bg-red-500');
});

it('has fallback', function () {
    expect($this->test)
        ->getFallback()->toBeNull()
        ->fallback('test')->toBe($this->test)
        ->getFallback()->toBe('test');
});

it('has sort', function () {
    expect($this->test)
        ->isSortable()->toBeFalse()
        ->getSort()->toBeNull()
        ->sorts()->toBe($this->test)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getName()->toBe($this->test->getName())
            ->getLabel()->toBe($this->test->getLabel())
            ->getParameter()->toBe($this->test->getParameter())
        )->sorts(false)->toBe($this->test)
        ->isSortable()->toBeFalse()
        ->getSort()->toBeNull();
});

it('has sort on different column', function () {
    expect($this->test)
        ->sorts('description')->toBe($this->test)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getName()->toBe('description')
            ->getLabel()->toBe($this->test->getLabel())
            ->getParameter()->toBe($this->test->getParameter())
        );
});

it('has sort instance', function () {
    expect($this->test)
        ->isSortable()->toBeFalse()
        ->getSort()->toBeNull()
        ->sorts(Sort::make('description'))->toBe($this->test)
        ->isSortable()->toBeTrue()
        ->getSort()->scoped(fn ($sort) => $sort
            ->toBeInstanceOf(Sort::class)
            ->getName()->toBe('description')
            ->getLabel()->toBe(ucfirst('description'))
            ->getParameter()->toBe('description')
        );
});

it('is searchable', function () {
    expect($this->test)
        ->isSearchable()->toBeFalse()
        ->searches()->toBe($this->test)
        ->isSearchable()->toBeTrue();
});

it('is filterable', function () {
    expect($this->test)
        ->isFilterable()->toBeFalse()
        ->filters()->toBe($this->test)
        ->isFilterable()->toBeTrue();
});

it('is select', function () {
    expect($this->test)
        ->isSelectable()->toBeTrue()
        ->selects(false)->toBe($this->test)
        ->isSelectable()->toBeFalse()
        ->getSelects()->toBe($this->test->getName())
        ->selects('test')->toBe($this->test)
        ->isSelectable()->toBeTrue()
        ->getSelects()->toBe('test');
});

it('applies', function () {
    expect($this->test->apply('value'))->toBe('value');

    expect($this->test->transformer(fn ($value) => $value * 2))
        ->toBeInstanceOf(Column::class)
        ->and($this->test->apply(2))->toBe(4);
});

it('has a query', function () {
    expect($this->test)
        ->query(fn ($query) => $query->where('name', 'value'))->toBe($this->test)
        ->getQuery()->toBeInstanceOf(\Closure::class);
});

it('has array representation', function () {
    expect($this->test->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => $this->param,
            'label' => ucfirst($this->param),
            'type' => null,
            'hidden' => false,
            'icon' => null,
            'toggles' => true,
            'active' => true,
            'sort' => null,
            'class' => null,
        ]);
});

it('has array representation with sort', function () {
    expect($this->test->sorts()->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => $this->param,
            'label' => ucfirst($this->param),
            'type' => null,
            'hidden' => false,
            'icon' => null,
            'toggles' => true,
            'active' => true,
            'sort' => [
                'active' => false,
                'direction' => null,
                'next' => $this->param,
            ],
            'class' => null,
        ]);
});
