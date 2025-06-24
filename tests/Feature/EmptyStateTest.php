<?php

declare(strict_types=1);

use Honed\Action\Operations\PageOperation;
use Honed\Table\EmptyState;

beforeEach(function () {
    $this->state = EmptyState::make();
});

it('makes with heading and description', function () {
    expect(EmptyState::make('Missing', 'Missing data'))
        ->getHeading()->toBe('Missing')
        ->getDescription()->toBe('Missing data');
});

it('has heading', function () {
    expect($this->state)
        ->getHeading()->toBe(EmptyState::DEFAULT_HEADING)
        ->heading('Missing')->toBe($this->state)
        ->getHeading()->toBe('Missing');
});

it('has description', function () {
    expect($this->state)
        ->getDescription()->toBe(EmptyState::DEFAULT_DESCRIPTION)
        ->description('Missing data')->toBe($this->state)
        ->getDescription()->toBe('Missing data');
});

it('adds operations', function () {
    expect($this->state)
        ->getOperations()->toBeEmpty()
        ->operations(PageOperation::make('create')->label('Create'))->toBe($this->state)
        ->operation([PageOperation::make('edit')->label('Edit')])->toBe($this->state)
        ->getOperations()->toHaveCount(2);
});

it('adds operation', function () {
    expect($this->state)
        ->getOperations()->toBeEmpty()
        ->operation(PageOperation::make('create')->label('Create'))->toBe($this->state)
        ->getOperations()->toHaveCount(1);
});

it('has array representation', function () {
    expect($this->state)
        ->toArray()->toEqual([
            'heading' => EmptyState::DEFAULT_HEADING,
            'description' => EmptyState::DEFAULT_DESCRIPTION,
            'icon' => null,
            'operations' => [],
        ]);
});

it('serializes to json', function () {
    expect($this->state)
        ->jsonSerialize()->toEqual([
            'heading' => EmptyState::DEFAULT_HEADING,
            'description' => EmptyState::DEFAULT_DESCRIPTION,
            'operations' => [],
        ]);
});
