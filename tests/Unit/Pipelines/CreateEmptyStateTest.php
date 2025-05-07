<?php

declare(strict_types=1);

use Honed\Refine\Filter;
use Honed\Table\Pipelines\CreateEmptyState;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Table;

beforeEach(function () {
    $this->pipe = new CreateEmptyState();
    $this->next = fn ($table) => $table;

    $this->table = Table::make()
        ->resource(Product::query());
});

it('has default empty state', function () {
    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getEmptyState())
        ->getTitle()->toBe(config('table.empty_state.title'))
        ->getMessage()->toBe(config('table.empty_state.message'))
        ->getIcon()->toBe(config('table.empty_state.icon'))
        ->getLabel()->toBeNull()
        ->getAction()->toBeNull();
});

it('has searching state', function () {
    $this->table->emptyState(fn ($emptyState) => $emptyState
        ->searching(fn ($emptyState) => $emptyState->title('Searching'))
    );

    $this->pipe->__invoke($this->table->term('term'), $this->next);

    expect($this->table->getEmptyState())
        ->getTitle()->toBe('Searching');
});

it('has filtering state', function () {
    $this->table->emptyState(fn ($emptyState) => $emptyState
        ->filtering('A refining message')
    );

    $filter = Filter::make('filter')->value('non-null');

    $this->pipe->__invoke($this->table->filters($filter), $this->next);

    expect($this->table->getEmptyState())
        ->getMessage()->toBe('A refining message');

});

it('has refining state', function () {
    $this->table->emptyState(fn ($emptyState) => $emptyState
        ->refining(fn ($emptyState) => $emptyState->title('Refining'))
    );

    $this->pipe->__invoke($this->table->term('term'), $this->next);

    expect($this->table->getEmptyState())
        ->getTitle()->toBe('Refining');

});
