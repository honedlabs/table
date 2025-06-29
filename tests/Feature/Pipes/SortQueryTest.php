<?php

declare(strict_types=1);

use Honed\Refine\Pipes\SortQuery;
use Honed\Refine\Sorts\Sort;
use Honed\Table\Table;
use Illuminate\Support\Facades\Request;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->pipe = new SortQuery();

    $this->name = 'name';

    $this->table = Table::make()
        ->for(User::class)
        ->sorts(Sort::make($this->name));
});

it('needs a sort key', function () {
    $request = Request::create('/', 'GET', [
        'invalid' => $this->name,
    ]);

    $this->pipe->instance($this->table->request($request));

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->orders)
        ->toBeEmpty();
});

it('applies sort', function () {
    $request = Request::create('/', 'GET', [
        $this->table->getSortKey() => $this->name,
    ]);

    $this->pipe->instance($this->table->request($request));

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->orders)
        ->toBeOnlyOrder($this->name, Sort::ASCENDING);
});

it('applies default sort', function () {
    $name = 'price';

    $request = Request::create('/', 'GET', [
        $this->table->getSortKey() => $name,
    ]);

    $this->pipe->instance($this->table
            ->sorts(Sort::make($name)->default())
            ->request($request)
    );

    $this->pipe->run();

    expect($this->table->getBuilder()->getQuery()->orders)
        ->toBeOnlyOrder($name, Sort::ASCENDING);
});
