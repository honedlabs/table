<?php

declare(strict_types=1);

use Honed\Refine\Filters\Filter;
use Honed\Table\EmptyState;
use Honed\Table\Pipes\CreateEmptyState;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->pipe = new CreateEmptyState();

    $this->table = ProductTable::make()
        ->whenEmptyStateSearching(fn ($emptyState) => $emptyState->heading('Searching'))
        ->whenEmptyStateFiltering(fn ($emptyState) => $emptyState->heading('Filtering'));

    $this->table->setPagination([
        'empty' => false,
    ]);
});

it('requires records to create empty state', function () {
    $this->table->setPagination([
        'empty' => true,
    ]);

    $this->pipe->run($this->table);

    expect($this->table)
        ->isEmpty()->toBeTrue()
        ->getEmptyState()->toBeNull();
});

it('creates empty state', function () {
    $this->pipe->run($this->table);

    expect($this->table->getEmptyState())
        ->toBeInstanceOf(EmptyState::class)
        ->getHeading()->toBe(EmptyState::DEFAULT_HEADING)
        ->getDescription()->toBe(EmptyState::DEFAULT_DESCRIPTION);
});

it('has searching state', function () {
    $this->table->setTerm('term');

    $this->pipe->run(
        $this->table
    );

    expect($this->table)
        ->isSearching()->toBeTrue()
        ->getEmptyState()
        ->scoped(fn ($emptyState) => $emptyState
            ->toBeInstanceOf(EmptyState::class)
            ->getHeading()->toBe('Searching')
        );
});

it('has filtering state', function () {

    $this->pipe->run(
        $this->table->filters(
            Filter::make('filter')
                ->value('value')
                ->active(true)
        )
    );

    expect($this->table)
        ->isFiltering()->toBeTrue()
        ->getEmptyState()
        ->scoped(fn ($emptyState) => $emptyState
            ->toBeInstanceOf(EmptyState::class)
            ->getHeading()->toBe('Filtering')
        );
});

it('has refining state', function () {
    $this->table->setTerm('term');

    $this->pipe->run(
        $this->table

            ->emptyState(EmptyState::make()
                ->whenRefining(fn ($emptyState) => $emptyState->heading('Refining'))
            )
    );

    expect($this->table)
        ->isSearching()->toBeTrue()
        ->getEmptyState()
        ->scoped(fn ($emptyState) => $emptyState
            ->toBeInstanceOf(EmptyState::class)
            ->getHeading()->toBe('Refining')
        );
});
