<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Concerns\HasColumns;

class HasColumnsTest
{
    use HasColumns;

    protected $columns;
}

class HasColumnsMethodTest extends HasColumnsTest
{
    public function columns(): array
    {
        return [
            Column::make('test'),
            Column::make('test2')->sortable(),
            Column::make('test3')->searchable(),
            Column::make('test4')->key(),
            Column::make('test5')->active(),
        ];
    }
}

beforeEach(function () {
    $this->test = new HasColumnsTest;
    $this->method = new HasColumnsMethodTest;
});

it('is empty by default', function () {
    expect($this->test)
        ->hasColumns()->toBeFalse();

    expect($this->method)
        ->hasColumns()->toBeTrue();
});

it('sets columns', function () {
    $this->test->setColumns([Column::make('test')]);

    expect($this->test)
        ->hasColumns()->toBeTrue()
        ->getColumns()->first()->scoped(fn ($column) => $column
            ->toBeInstanceOf(Column::class)
            ->getName()->toBe('test')
        );
});

it('rejects null columns', function () {
    $this->test->setColumns([Column::make('test')]);
    $this->test->setColumns(null);

    expect($this->test)
        ->hasColumns()->toBeTrue();
});

it('gets columns from method', function () {
    expect($this->method)
        ->hasColumns()->toBeTrue()
        ->getColumns()->toBeCollection();
});

it('gets sortable columns', function () {
    expect($this->method)
        ->hasColumns()->toBeTrue()
        ->getSortableColumns()->scoped(fn ($columns) => $columns
            ->toBeCollection()
            ->first()->scoped(fn ($column) => $column
                ->toBeInstanceOf(Column::class)
                ->getName()->toBe('test2')
            )
        );
});

it('gets searchable columns', function () {
    expect($this->method)
        ->hasColumns()->toBeTrue()
        ->getSearchableColumns()->scoped(fn ($columns) => $columns
            ->toBeCollection()
            ->first()->toBe('test3')
        );
});

it('gets key column', function () {
    expect($this->method)
        ->hasColumns()->toBeTrue()
        ->getKeyColumn()->scoped(fn ($column) => $column
            ->toBeInstanceOf(Column::class)
            ->getName()->toBe('test4')
        );
});
