<?php

declare(strict_types=1);

use Honed\Table\EmptyState;

beforeEach(function () {
    $this->state = EmptyState::make();
});

it('has title', function () {
    expect($this->state)
        ->getTitle()->toBe(config('table.empty_state.title'))
        ->title('Title')->toBe($this->state)
        ->getTitle()->toBe('Title');
});

it('has message', function () {
    expect($this->state)
        ->getMessage()->toBe(config('table.empty_state.message'))
        ->message('Message')->toBe($this->state)
        ->getMessage()->toBe('Message');
});

it('has icon', function () {
    expect($this->state)
        ->getIcon()->toBe(config('table.empty_state.icon'))
        ->icon('Icon')->toBe($this->state)
        ->getIcon()->toBe('Icon');
});

it('has action', function () {
    expect($this->state)
        ->getLabel()->toBeNull()
        ->getAction()->toBeNull()
        ->action('Create', '/products')->toBe($this->state)
        ->getLabel()->toBe('Create')
        ->getAction()->toBe('/products');
});

it('has refining state', function () {
    expect($this->state)
        ->getRefiningState()->toBe(config('table.empty_state.refining'))
        ->whenRefining(fn ($state) => $state->title('Refining'))->toBe($this->state)
        ->getRefiningState()->toBeInstanceOf(\Closure::class);
});

it('has filtering state', function () {
    expect($this->state)
        ->getFilteringState()->toBeNull()
        ->whenFiltering(fn ($state) => $state->title('Filtering'))->toBe($this->state)
        ->getFilteringState()->toBeInstanceOf(\Closure::class);
});

it('has searching state', function () {
    expect($this->state)
        ->getSearchingState()->toBe(config('table.empty_state.searching'))
        ->whenSearching(fn ($state) => $state->title('Searching'))->toBe($this->state)
        ->getSearchingState()->toBeInstanceOf(\Closure::class);
});

it('has array representation', function () {
    expect($this->state)
        ->toArray()->toEqual([
            'title' => config('table.empty_state.title'),
            'message' => config('table.empty_state.message'),
            'icon' => config('table.empty_state.icon'),
            'label' => null,
            'action' => null,
        ]);
});