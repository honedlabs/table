<?php

use Honed\Table\Url\Url;
use Honed\Table\Confirm\Confirm;
use Honed\Table\Actions\InlineAction;

beforeEach(function () {
    $this->confirmable = InlineAction::make('test');
});

it('has no confirm by default', function () {
    expect($this->confirmable->isConfirmable())->toBeFalse();
    expect($this->confirmable->isNotConfirmable())->toBeTrue();
});

it('can set a confirm using a string', function () {
    expect($this->confirmable->confirm('The description goes heres'))->toBeInstanceOf(InlineAction::class)
        ->isConfirmable()->toBeTrue();

    expect($this->confirmable->getConfirm()->getDescription())->toBe('The description goes heres');
});

it('can set a confirm using a Confirm instance', function () {
    expect($this->confirmable->confirm(Confirm::make()->description('The description goes heres')))
        ->toBeInstanceOf(InlineAction::class)
        ->isConfirmable()->toBeTrue();
    
    expect($this->confirmable->getConfirm()->getDescription())->toBe('The description goes heres');
});

it('can be set using key value pairs', function () {
    expect($this->confirmable->confirm([
        'title' => 'The title goes heres',
        'description' => 'The description goes heres',
        'success' => 'Success',
    ]))->toBeInstanceOf(InlineAction::class)
        ->isConfirmable()->toBeTrue();

    expect($this->confirmable->getConfirm())
        ->getTitle()->toBe('The title goes heres')
        ->getDescription()->toBe('The description goes heres')
        ->getSuccess()->toBe('Success');
});

it('can be set using a closure', function () {
    expect($this->confirmable->confirm(fn (Confirm $confirm) => $confirm->title('The title goes heres')->description('The description goes heres')->success('Success')))
        ->toBeInstanceOf(InlineAction::class)
        ->isConfirmable()->toBeTrue();
    
    expect($this->confirmable->getConfirm())
        ->getTitle()->toBe('The title goes heres')
        ->getDescription()->toBe('The description goes heres')
        ->getSuccess()->toBe('Success');
});

// it('can resolve a confirm to a record', function () {
//     expect($this->confirmable->confirm('/products'))
//         ->toBeInstanceOf(InlineAction::class)
//         ->isUrlable()->toBeTrue();

//     expect($this->confirmable->getUrl())
//         ->getUrl()->toBe('/products')
//         ->isNamed()->toBeFalse();
// });

it('can make a new confirm instance', function () {
    expect($this->confirmable->makeConfirm())->toBeInstanceOf(Confirm::class);
    expect($this->confirmable->isConfirmable())->toBeTrue();
});