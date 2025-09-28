<?php

declare(strict_types=1);

use Honed\Refine\Pipes\SearchQuery;
use Honed\Refine\Searches\Search;
use Honed\Table\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->pipe = new SearchQuery();

    $this->query = 'search+value';

    $this->term = Str::of($this->query)
        ->replace('+', ' ')
        ->trim()
        ->toString();

    $this->table = Table::make()
        ->for(Product::class)
        ->searches([
            Search::make('name'),
            Search::make('description'),
        ]);

    $this->table->define(); // @TODO
});

it('needs a search term', function () {
    $request = Request::create('/', 'GET', [
        'invalid' => $this->query,
    ]);

    $this->pipe->through($this->table->request($request));

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->toBeEmpty();

    expect($this->table->getSearchTerm())
        ->toBeNull();
});

it('applies search', function () {
    $request = Request::create('/', 'GET', [
        $this->table->getSearchKey() => $this->query,
    ]);

    $this->pipe->through($this->table->request($request));

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}->scoped(fn ($where) => $where
        ->toBeArray()
        ->toHaveKeys(['type', 'query', 'boolean'])
        ->{'type'}->toBe('Nested')
        ->{'boolean'}->toBe('and')
        ->{'query'}
        ->scoped(fn ($query) => $query
            ->toBeInstanceOf(Builder::class)
            ->wheres
            ->scoped(fn ($wheres) => $wheres
                ->toBeArray()
                ->toHaveCount(2)
                ->{0}->toBeSearch('name', 'and')
                ->{1}->toBeSearch('description', 'or')
            )
        )
        );

    expect($this->table->getSearchTerm())
        ->toBe($this->term);
});

it('applies search with matching', function () {
    $this->table->matchable();

    $request = Request::create('/', 'GET', [
        $this->table->getSearchKey() => $this->query,
        $this->table->getMatchKey() => 'name',
    ]);

    $this->pipe->through($this->table->request($request));

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->toBeArray()
        ->toHaveCount(1)
        ->{0}->scoped(fn ($where) => $where
        ->toBeArray()
        ->toHaveKeys(['type', 'query', 'boolean'])
        ->{'type'}->toBe('Nested')
        ->{'boolean'}->toBe('and')
        ->{'query'}
        ->scoped(fn ($query) => $query
            ->toBeInstanceOf(Builder::class)
            ->wheres
            ->scoped(fn ($wheres) => $wheres
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}->toBeSearch('name', 'and')
            )
        )
        );

    expect($this->table->getSearchTerm())
        ->toBe($this->term);
});
