<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Pipes\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->pipe = new Query();

    $this->table = ProductTable::make();

    $this->table->setHeadings([
        Column::make('users_count')->count(),

        Column::make('user_exists')->exists(),

        Column::make('public_id')
            ->query(fn (Builder $query) => $query->where('public_id', '!=', null)
            ),
    ]);
});

it('does not apply without headings', function () {
    $this->table->setHeadings([]);

    $this->pipe->run($this->table);

    expect($this->table->getBuilder()->getQuery())
        ->wheres->toBeEmpty()
        ->columns->toBeEmpty();
});

it('applies heading queries', function () {
    $this->pipe->run($this->table);

    expect($this->table->getBuilder()->getQuery())
        ->wheres
        ->scoped(fn ($wheres) => $wheres
            ->toHaveCount(1)
            ->{0}
            ->scoped(fn ($where) => $where
                ->toHaveCount(3)
                ->{'type'}->toBe('NotNull')
                ->{'column'}->toBe('public_id')
            )
        )
        ->columns
        ->scoped(fn ($columns) => $columns
            ->toHaveCount(3)
            ->{0}->toBe('products.*')
            ->{1}
            ->scoped(fn ($column) => $column
                ->toBeInstanceOf(Expression::class)
                ->getValue(new Grammar())->toContain('users_count')
            )
            ->{2}
            ->scoped(fn ($column) => $column
                ->toBeInstanceOf(Expression::class)
                ->getValue(new Grammar())->toContain('user_exists')
            )
        );
});
