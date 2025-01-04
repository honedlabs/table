<?php

use Honed\Table\Actions\InlineAction;
use Honed\Table\Tests\Stubs\Product;

beforeEach(function () {
    $this->action = InlineAction::make('test');
});

it('has no action by default', function () {
    expect($this->action->hasAction())->toBeFalse();
});

it('can be set to have an action', function () {
    expect($this->action->action(fn () => true))->toBeInstanceOf(InlineAction::class)
        ->hasAction()->toBeTrue();
});

it('can retrieve the action', function () {
    expect($this->action->getAction())->toBeNull();
    $this->action->action(fn () => true);
    expect($this->action->getAction())->toBeInstanceOf(\Closure::class);
});

it('can be set using setter', function () {
    $this->action->setAction(fn () => true);
    expect($this->action->hasAction())->toBeTrue();
});

it('does not accept null actions', function () {
    $this->action->setAction(null);
    expect($this->action->hasAction())->toBeFalse();
});

class ActionableTestClass
{
    public function __invoke()
    {
        return true;
    }
}

it('accepts invokable class actions', function () {
    expect($this->action->action(ActionableTestClass::class))->toBeInstanceOf(InlineAction::class)
        ->hasAction()->toBeTrue();
});

it('can be applied', function () {
    $product = product();
    $this->action->action(fn (Product $product) => $product->update(['name' => 'Updated']));
    $this->action->applyAction($product, Product::class);
    expect($product->name)->toBe('Updated');
});
