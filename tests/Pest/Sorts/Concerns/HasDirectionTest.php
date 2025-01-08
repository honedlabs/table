<?php

use Honed\Table\Sorts\Concerns\HasDirection;

class HasDirectionTest
{
    use HasDirection;
}

beforeEach(function () {
    HasDirectionTest::sortByAscending();
    $this->test = new HasDirectionTest();
});

it('has no direction by default', function () {
    expect($this->test)
        ->getDirection()->toBeNull()
        ->hasDirection()->toBeFalse();
});


it('sets direction', function () {
    $this->test->setDirection(HasDirectionTest::Descending);
    expect($this->test)
        ->getDirection()->toBe(HasDirectionTest::Descending)
        ->hasDirection()->toBeTrue();
});

it('chains direction', function () {
    expect($this->test->direction(HasDirectionTest::Descending))->toBeInstanceOf(HasDirectionTest::class)
        ->getDirection()->toBe(HasDirectionTest::Descending)
        ->hasDirection()->toBeTrue();
});

it('rejects invalid directions', function () {
    $this->test->setDirection(HasDirectionTest::Descending);
    $this->test->setDirection('invalid');
    expect($this->test)
        ->getDirection()->toBe(HasDirectionTest::Descending)
        ->hasDirection()->toBeTrue();
});


it('has shorthand `desc`', function () {
    expect($this->test->desc())->toBeInstanceOf(HasDirectionTest::class)
        ->getDirection()->toBe(HasDirectionTest::Descending)
        ->hasDirection()->toBeTrue();
});

it('has shorthand `asc`', function () {
    expect($this->test->asc())->toBeInstanceOf(HasDirectionTest::class)
        ->getDirection()->toBe(HasDirectionTest::Ascending)
        ->hasDirection()->toBeTrue();
});

it('can be globally configured for descending', function () {
    HasDirectionTest::sortByDescending();
    expect($this->test->getDefaultDirection())->toBe(HasDirectionTest::Descending);
});

it('can be globally configured for ascending', function () {
    HasDirectionTest::sortByAscending();
    expect($this->test->getDefaultDirection())->toBe(HasDirectionTest::Ascending);
});

it('has no active direction by default', function () {
    expect($this->test->getActiveDirection())->toBeNull();
});

it('sets active direction', function () {
    $this->test->setActiveDirection(HasDirectionTest::Descending);
    expect($this->test->getActiveDirection())->toBe(HasDirectionTest::Descending);
});

it('rejects invalid active directions', function () {
    $this->test->setActiveDirection(HasDirectionTest::Descending);
    $this->test->setActiveDirection('invalid');
    expect($this->test->getActiveDirection())->toBe(HasDirectionTest::Descending);
});

it('checks if is agnostic', function () {
    expect($this->test->isAgnostic())->toBeTrue();
    $this->test->setDirection(HasDirectionTest::Descending);
    expect($this->test->isAgnostic())->toBeFalse();
});

it('chains agnostic', function () {
    expect($this->test->agnostic())->toBeInstanceOf(HasDirectionTest::class)
        ->getDirection()->toBeNull()
        ->isAgnostic()->toBeTrue();
});
