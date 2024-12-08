<?php

use Honed\Table\Sorts\Sort;
use Illuminate\Support\Facades\Request;

// beforeEach(function () {
//     $request = Request::create('/', 'GET', ['email' => 'test@example.com']);
//     Request::swap($request);
//     $this->sort = Sort::make('name');
// });

// it('can be instantiated', function () {
//     expect(new Sort('email'))->toBeInstanceOf(Sort::class)
//         ->getAttribute()->toBe('email')
//         ->getLabel()->toBe('Email');
// });

// it('can be made', function () {
//     expect(Sort::make('email', 'User email'))->toBeInstanceOf(Sort::class)
//         ->getAttribute()->toBe('email')
//         ->getLabel()->toBe('User email');
// });

// it('retrieves the value from the request', function () {
//     expect($this->sort->getValueFromRequest())->toBe('test@example.com');
// });

// it('determines if the filter should be applied', function () {
//     expect($this->sort->isFiltering('test@example.com'))->toBeTrue();
//     expect($this->sort->isFiltering(null))->toBeFalse();
// });

// it('differentiates between dot notation attributes', function () {
//     expect(Filter::make('user.email', 'User email'))
//         ->getAttribute()->toBe('user.email')
//         ->getAlias()->toBeNull()
//         ->getValueFromRequest()->toBe('test@example.com'); // resolves as just email
// });

// it('has an array form', function () {
//     // The active status is only applied when the filter itself is applied
//     expect($this->sort->toArray())->toEqual([
//         'name' => 'email',
//         'label' => 'Email',
//         'type' => 'filter',
//         'isActive' => false,
//         'value' => null,
//         'meta' => [],
//     ]);
// });
