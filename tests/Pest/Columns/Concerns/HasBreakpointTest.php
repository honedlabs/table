<?php

use Honed\Table\Columns\Column;
use Honed\Table\Columns\Concerns\HasBreakpoint;

class HasBreakpointTest
{
    use HasBreakpoint;
}

beforeEach(function () {
    $this->test = new HasBreakpointTest;
});

it('has no `breakpoint` by default', function () {
    expect($this->test)
        ->getBreakpoint()->toBeNull()
        ->hasBreakpoint()->toBeFalse();
});

it('sets breakpoint', function () {
    $this->test->setBreakpoint(HasBreakpointTest::ExtraSmall);
    expect($this->test)
        ->getBreakpoint()->toBe(HasBreakpointTest::ExtraSmall)
        ->hasBreakpoint()->toBeTrue();
});

it('rejects null values', function () {
    $this->test->setBreakpoint(HasBreakpointTest::ExtraSmall);
    $this->test->setBreakpoint(null);
    expect($this->test)
        ->getBreakpoint()->toBe(HasBreakpointTest::ExtraSmall)
        ->hasBreakpoint()->toBeTrue();
});

it('chains breakpoint', function () {
    expect($this->test->breakpoint(HasBreakpointTest::ExtraSmall))->toBeInstanceOf(HasBreakpointTest::class)
        ->getBreakpoint()->toBe(HasBreakpointTest::ExtraSmall)
        ->hasBreakpoint()->toBeTrue();
});

it('has shorthand `xs`', function () {
    expect($this->test->xs())->toBeInstanceOf(HasBreakpointTest::class)
        ->getBreakpoint()->toBe(HasBreakpointTest::ExtraSmall)
        ->hasBreakpoint()->toBeTrue();
});

it('has shorthand `sm`', function () {
    expect($this->test->sm())->toBeInstanceOf(HasBreakpointTest::class)
        ->getBreakpoint()->toBe(HasBreakpointTest::Small)
        ->hasBreakpoint()->toBeTrue();
});

it('has shorthand `md`', function () {
    expect($this->test->md())->toBeInstanceOf(HasBreakpointTest::class)
        ->getBreakpoint()->toBe(HasBreakpointTest::Medium)
        ->hasBreakpoint()->toBeTrue();
});

it('has shorthand `lg`', function () {
    expect($this->test->lg())->toBeInstanceOf(HasBreakpointTest::class)
        ->getBreakpoint()->toBe(HasBreakpointTest::Large)
        ->hasBreakpoint()->toBeTrue();
});

it('has shorthand `xl`', function () {
    expect($this->test->xl())->toBeInstanceOf(HasBreakpointTest::class)
        ->getBreakpoint()->toBe(HasBreakpointTest::ExtraLarge)
        ->hasBreakpoint()->toBeTrue();
});

it('rejects invalid breakpoints', function () {
    $this->test->breakpoint('Invalid');
})->throws(\InvalidArgumentException::class);




