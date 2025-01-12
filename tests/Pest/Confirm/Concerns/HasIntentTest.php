<?php

use Honed\Table\Confirm\Concerns\HasIntent;
use Honed\Table\Confirm\Confirm;

class HasIntentTest
{
    use HasIntent;
}

beforeEach(function () {
    $this->confirm = Confirm::make();
});

beforeEach(function () {
    $this->test = new HasIntentTest;
});

it('has no intent by default', function () {
    expect($this->test)
        ->getIntent()->toBeNull()
        ->hasIntent()->toBeFalse();
});

it('sets intent', function () {
    $this->test->setIntent('Intent');
    expect($this->test)
        ->getIntent()->toBe('Intent')
        ->hasIntent()->toBeTrue();
});

it('rejects null values', function () {
    $this->test->setIntent('Intent');
    $this->test->setIntent(null);
    expect($this->test)
        ->getIntent()->toBe('Intent')
        ->hasIntent()->toBeTrue();
});

it('chains intent', function () {
    expect($this->test->intent('Intent'))->toBeInstanceOf(HasIntentTest::class)
        ->getIntent()->toBe('Intent')
        ->hasIntent()->toBeTrue();
});

it('has shorthand `constructive`', function () {
    expect($this->test->constructive())->toBeInstanceOf(HasIntentTest::class)
        ->getIntent()->toBe(HasIntentTest::Constructive)
        ->hasIntent()->toBeTrue();
});

it('has shorthand `destructive`', function () {
    expect($this->test->destructive())->toBeInstanceOf(HasIntentTest::class)
        ->getIntent()->toBe(HasIntentTest::Destructive)
        ->hasIntent()->toBeTrue();
});

it('has shorthand `informative`', function () {
    expect($this->test->informative())->toBeInstanceOf(HasIntentTest::class)
        ->getIntent()->toBe(HasIntentTest::Informative)
        ->hasIntent()->toBeTrue();
});
