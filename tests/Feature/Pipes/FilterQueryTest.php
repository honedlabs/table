<?php

declare(strict_types=1);

use Honed\Refine\Filters\Filter;
use Honed\Refine\Pipes\FilterQuery;
use Honed\Table\Table;
use Illuminate\Support\Facades\Request;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->pipe = new FilterQuery();

    $this->name = 'price';

    $this->value = 100;

    $this->table = Table::make()
        ->for(User::class)
        ->filters(Filter::make($this->name)->int());

    $this->table->define(); // @TODO
});

it('needs a filter key', function () {
    $request = Request::create('/', 'GET', [
        'invalid' => $this->value,
    ]);

    $this->pipe->through($this->table->request($request));

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->toBeEmpty();
});

it('applies filter', function () {
    $request = Request::create('/', 'GET', [
        $this->name => $this->value,
    ]);

    $this->pipe->through($this->table->request($request));

    expect($this->table->getBuilder()->getQuery()->wheres)
        ->toBeOnlyWhere($this->name, $this->value);
});
