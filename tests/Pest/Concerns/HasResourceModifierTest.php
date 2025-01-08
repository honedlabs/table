<?php

declare(strict_types=1);

use Honed\Table\Tests\Stubs\Product;
use Illuminate\Database\Eloquent\Builder;
use Honed\Table\Concerns\HasResourceModifier;

class HasResourceModifierTest
{
    use HasResourceModifier;
}

class HasResourceModifierMethodTest extends HasResourceModifierTest
{
    public function modifier(Builder $builder): void
    {
        $builder->where('created_at', '>', '2000-01-01');
    }
}

beforeEach(function () {
    $this->test = new HasResourceModifierTest();
    $this->method = new HasResourceModifierMethodTest();
    $this->builder = Product::query();
});

it('checks if it exists', function () {
    expect($this->test)
        ->hasResourceModifier()->toBeFalse();

    expect($this->method)
        ->hasResourceModifier()->toBeTrue();
});

it('does not modify by default', function () {
    $this->test->modifyResource($this->builder);

    expect($this->builder->getQuery()->wheres)
        ->toBeEmpty();
});

it('sets the resource modifier', function () {
    $this->test->setModifier(fn ($builder) => $builder->where('created_at', '>', '2000-01-01'));

    expect($this->test)
        ->hasResourceModifier()->toBeTrue();

    $this->test->modifyResource($this->builder);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => 'created_at',
            'operator' => '>',
            'value' => '2000-01-01',
            'boolean' => 'and',
        ]);
});

it('rejects null values', function () {
    $this->test->setModifier(fn ($builder) => $builder->where('created_at', '>', '2000-01-01'));
    $this->test->setModifier(null);

    expect($this->test)
        ->hasResourceModifier()->toBeTrue();

    $this->test->modifyResource($this->builder);
    
    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => 'created_at',
            'operator' => '>',
            'value' => '2000-01-01',
            'boolean' => 'and',
        ]);
});

it('acts before retrieval', function () {
    $this->method->modifyResource($this->builder);

    expect($this->builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->toEqual([
            'type' => 'Basic',
            'column' => 'created_at',
            'operator' => '>',
            'value' => '2000-01-01',
            'boolean' => 'and',
        ]);
});
