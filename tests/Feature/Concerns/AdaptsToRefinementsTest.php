<?php

declare(strict_types=1);

use Honed\Table\EmptyState;

beforeEach(function () {
    $this->state = EmptyState::make();
});

it('has refining callback', function () {
    expect($this->state)
        ->getRefiningCallback()->toBeNull()
        ->whenRefining(fn () => $this->state)
        ->getRefiningCallback()->toBeInstanceOf(Closure::class);
});

it('has filtering callback', function () {
    expect($this->state)
        ->getFilteringCallback()->toBeNull()
        ->whenFiltering(fn () => $this->state)
        ->getFilteringCallback()->toBeInstanceOf(Closure::class);
});

it('has searching callback', function () {
    expect($this->state)
        ->getSearchingCallback()->toBeNull()
        ->whenSearching(fn () => $this->state)
        ->getSearchingCallback()->toBeInstanceOf(Closure::class);
});
