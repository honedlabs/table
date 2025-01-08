<?php

declare(strict_types=1);

use Honed\Table\Columns\Concerns\IsSearchable;

class IsSearchableTest
{
    use IsSearchable;
}

beforeEach(function () {
    $this->test = new IsSearchableTest;
});

it('is not `searchable` by default', function () {
    expect($this->test->isSearchable())->toBeFalse();
});

it('sets searchable', function () {
    $this->test->setSearchable(true);
    expect($this->test->isSearchable())->toBeTrue();
});

it('chains searchable', function () {
    expect($this->test->searchable())->toBeInstanceOf(IsSearchableTest::class)
        ->isSearchable()->toBeTrue();
});