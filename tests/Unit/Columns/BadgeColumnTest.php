<?php

declare(strict_types=1);

use Honed\Table\Columns\BadgeColumn;
use Honed\Table\Tests\Stubs\Status;

beforeEach(function () {
    $this->column = BadgeColumn::make('status');
});

it('sets up', function () {
    expect($this->column)
        ->getType()->toBe('badge');
});

it('has map', function () {
    expect($this->column)
        ->getMap()->toBeEmpty()
        ->map(['active' => 'success', 'inactive' => 'danger'])->toBe($this->column)
        ->getMap()->toEqual([
            'active' => 'success',
            'inactive' => 'danger',
        ]);
});

it('has default', function () {
    expect($this->column)
        ->getDefault()->toBe('default')
        ->default('success')->toBe($this->column)
        ->getDefault()->toBe('success');
});

it('has extra', function () {
    expect($this->column)
        ->defineExtra()->toBeInstanceOf(\Closure::class)
        ->getExtra(['value' => 'value'])->toEqual([
            'variant' => 'default',
        ]);
});

