<?php

declare(strict_types=1);

use Honed\Refine\Pipes\SearchQuery;
use Honed\Refine\Searches\Search;
use Honed\Table\Table;
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
});

it('needs a search term', function () {
    $request = Request::create('/', 'GET', [
        'invalid' => $this->query,
    ]);

    $this->pipe->instance($this->table->request($request));

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->toBeEmpty();

    expect($this->table->getSearchTerm())
        ->toBeNull();
});

it('applies search', function () {
    $request = Request::create('/', 'GET', [
        $this->table->getSearchKey() => $this->query,
    ]);

    $this->pipe->instance($this->table->request($request));

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->{0}->toBeSearch('name', 'and')
        ->{1}->toBeSearch('description', 'or');

    expect($this->table->getSearchTerm())
        ->toBe($this->term);
});

it('applies search with matching', function () {
    $this->table->matchable();

    $request = Request::create('/', 'GET', [
        $this->table->getSearchKey() => $this->query,
        $this->table->getMatchKey() => 'name',
    ]);

    $this->pipe->instance($this->table->request($request));

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->toBeOnlySearch('name');

    expect($this->table->getSearchTerm())
        ->toBe($this->term);
});
