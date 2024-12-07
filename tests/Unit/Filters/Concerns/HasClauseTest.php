<?php

use Honed\Table\Filters\Filter;
use Honed\Table\Filters\Enums\Clause;

beforeEach(function () {
    $this->filter = Filter::make('name');
});

it('can set a clause', function () {
    expect($this->filter->clause(Clause::Contains))->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::Contains);
});

it('can be set using setter', function () {
    $this->filter->setClause(Clause::Contains);
    expect($this->filter->getClause())->toBe(Clause::Contains);
});

it('does not accept null values', function () {
    expect($this->filter->getClause())->toBe(Clause::Is);
    $this->filter->setClause(null);
    expect($this->filter->getClause())->toBe(Clause::Is);
});

it('checks if it has a clause', function () {
    expect($this->filter->hasClause())->toBeTrue();
    expect($this->filter->missingClause())->toBeFalse();
});

it('has shorthand for is', function () {
    expect($this->filter->is())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::Is);
});

it('has shorthand for isNot', function () {
    expect($this->filter->isNot())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::IsNot);
});

it('has shorthand for startsWith', function () {
    expect($this->filter->startsWith())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::StartsWith);
});

it('has alias for startsWith', function () {
    expect($this->filter->beginsWith())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::StartsWith);
});

it('has shorthand for endsWith', function () {
    expect($this->filter->endsWith())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::EndsWith);
});

it('has shorthand for contains', function () {
    expect($this->filter->contains())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::Contains);
});

it('has shorthand for doesNotContain', function () {
    expect($this->filter->doesNotContain())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::DoesNotContain);
});

it('has shorthand for json', function () {
    expect($this->filter->json())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::Json);
});

it('has shorthand for notJson', function () {
    expect($this->filter->notJson())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::NotJson);
});

it('has shorthand for jsonLength', function () {
    expect($this->filter->jsonLength())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::JsonLength);
});

it('has shorthand for fullText', function () {
    expect($this->filter->fullText())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::FullText);
});

it('has shorthand for jsonKey', function () {
    expect($this->filter->jsonKey())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::JsonKey);
});

it('has shorthand for notJsonKey', function () {
    expect($this->filter->notJsonKey())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::JsonNotKey);
});

it('has shorthand for jsonOverlap', function () {
    expect($this->filter->jsonOverlap())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::JsonOverlaps);
});

it('has alias for jsonOverlap', function () {
    expect($this->filter->jsonOverlaps())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::JsonOverlaps);
});

it('has shorthand for jsonDoesNotOverlap', function () {
    expect($this->filter->jsonDoesNotOverlap())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::JsonDoesNotOverlap);
});

it('has shorthand for like', function () {
    expect($this->filter->like())->toBeInstanceOf(Filter::class)
        ->getClause()->toBe(Clause::Like);
});
