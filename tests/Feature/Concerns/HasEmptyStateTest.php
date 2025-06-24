<?php

declare(strict_types=1);

use Honed\Action\Operations\PageOperation;
use Honed\Table\EmptyState;
use Honed\Table\Table;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->table = Table::make()->for(User::class);
});

it('has empty state', function () {
    expect($this->table)
        ->getEmptyState()->toBeNull()
        ->emptyState(EmptyState::make())->toBe($this->table)
        ->getEmptyState()
        ->scoped(fn ($state) => $state
            ->toBeInstanceOf(EmptyState::class)
            ->getHeading()->toBe(EmptyState::DEFAULT_HEADING)
        );
});

it('has empty state via closure', function () {
    expect($this->table)
        ->getEmptyState()->toBeNull()
        ->emptyState(fn (EmptyState $state) => $state->heading('Missing'))->toBe($this->table)
        ->getEmptyState()
        ->scoped(fn ($state) => $state
            ->toBeInstanceOf(EmptyState::class)
            ->getHeading()->toBe('Missing')
        );
});

it('sets empty state heading', function () {
    expect($this->table)
        ->emptyStateHeading('Missing')->toBe($this->table)
        ->getEmptyState()
        ->scoped(fn ($state) => $state
            ->toBeInstanceOf(EmptyState::class)
            ->getHeading()->toBe('Missing')
        );
});

it('sets empty state description', function () {
    expect($this->table)
        ->emptyStateDescription('Missing data')->toBe($this->table)
        ->getEmptyState()
        ->scoped(fn ($state) => $state
            ->toBeInstanceOf(EmptyState::class)
            ->getDescription()->toBe('Missing data')
        );
});

it('sets empty state icon', function () {
    expect($this->table)
        ->emptyStateIcon('heroicon-o-exclamation-triangle')->toBe($this->table)
        ->getEmptyState()
        ->scoped(fn ($state) => $state
            ->toBeInstanceOf(EmptyState::class)
            ->getIcon()->toBe('heroicon-o-exclamation-triangle')
        );
});

it('sets empty state operations', function () {
    expect($this->table)
        ->emptyStateOperations([
            PageOperation::make('create')->label('Create'),
        ])->toBe($this->table)
        ->getEmptyState()
        ->scoped(fn ($state) => $state
            ->toBeInstanceOf(EmptyState::class)
            ->getOperations()->toHaveCount(1)
        );
});

it('sets filtering callback', function () {
    expect($this->table)
        ->whenEmptyStateFiltering(fn (EmptyState $state) => $state->heading('Missing'))->toBe($this->table);
});

it('sets searching callback', function () {
    expect($this->table)
        ->whenEmptyStateSearching(fn (EmptyState $state) => $state->heading('Missing'))->toBe($this->table);
});

it('sets refining callback', function () {
    expect($this->table)
        ->whenEmptyStateRefining(fn (EmptyState $state) => $state->heading('Missing'))->toBe($this->table);
});

it('has array representation', function () {
    $state = EmptyState::make();

    expect($this->table)
        ->emptyStateToArray()->toBeNull()
        ->emptyState($state)
        ->emptyStateToArray()->toEqual($state->toArray());
});
