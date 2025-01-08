<?php

declare(strict_types=1);

use Honed\Table\Filters\Filter;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    $this->name = 'email';
    $this->value = 'test@example.com';
    $this->filter = Filter::make($this->name);
    Request::swap(Request::create('/', 'GET', [$this->name => $this->value]));
});

it('can be instantiated', function () {
    expect(new Filter($this->name))
        ->toBeInstanceOf(Filter::class)
        ->getAttribute()->toBe('email')
        ->getLabel()->toBe('Email')
        ->getParameterName()->toBe('email')
        ->isActive()->toBeFalse();
});

it('can be made', function () {
    expect(Filter::make('email', 'User email'))
        ->toBeInstanceOf(Filter::class)
        ->getAttribute()->toBe('email')
        ->getLabel()->toBe('User email')
        ->getParameterName()->toBe('email')
        ->isActive()->toBeFalse();
});

it('gets value from request', function () {
    expect($this->filter->getValueFromRequest())
        ->toBe($this->value);
});

it('determines if the filter should be applied', function () {
    expect($this->filter->isFiltering($this->value))
        ->toBeTrue();
    expect($this->filter->isFiltering(null))
        ->toBeFalse();
});

it('differentiates between dot notation attributes', function () {
    expect(Filter::make('user.email'))
        ->getAttribute()->toBe('user.email')
        ->getAlias()->toBeNull()
        ->getValueFromRequest()->toBe($this->value); // resolves as just email
});

it('has an array representation', function () {
    // The active status is only applied when the filter itself is applied
    expect($this->filter->toArray())->toEqual([
        'name' => $this->name,
        'label' => 'Email',
        'type' => 'filter',
        'active' => false,
        'value' => null,
        'meta' => [],
    ]);
});
