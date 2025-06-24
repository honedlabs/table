<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Honed\Table\Pipes\Toggle;
use Honed\Table\Table;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Workbench\App\Models\Product;

beforeEach(function () {
    $this->pipe = new Toggle();

    $this->table = Table::make()
        ->for(Product::class)
        ->orderable()
        ->columns([
            Column::make('id'),
            Column::make('name'),
            Column::make('description'),
            Column::make('price'),
        ]);
});

it('uses defaults', function () {
    $this->pipe->run(
        $this->table
    );

    expect(array_map(fn ($column) => $column->getName(), $this->table->getColumns()))
        ->toBe(['id', 'name', 'description', 'price']);

})->with([
    'basic' => fn () => $this->table,

    'clear' => function () {
        $request = Request::create('/', 'GET', [
            $this->table->getColumnKey() => null,
        ]);

        return $this->table->request($request);
    },
    'orderable' => function () {
        $request = Request::create('/', 'GET', [
            $this->table->getColumnKey() => ['price'],
        ]);

        return $this->table->request($request)->orderable(false);
    },
]);

it('retrieves from sources', function ($table) {
    $this->pipe->run($table);

    expect(array_map(fn ($column) => $column->getName(), $table->getColumns()))
        ->toBe(['price', 'id', 'name', 'description']);
})->with([
    'request' => function () {
        $request = Request::create('/', 'GET', [
            $this->table->getColumnKey() => ['price'],
        ]);

        return $this->table->request($request);
    },

    'scope' => function () {
        $this->table->scope('scope');

        $request = Request::create('/', 'GET', [
            $this->table->getColumnKey() => ['price'],
        ]);

        return $this->table->request($request);
    },

    'session' => function () {
        Session::put(
            $this->table->getPersistKey(),
            [$this->table->getColumnKey() => ['price']]
        );

        return $this->table->persistColumnsInSession();
    },

    'cookie' => function () {
        $request = Request::create('/', 'GET', cookies: [
            $this->table->getPersistKey() => json_encode([$this->table->getColumnKey() => ['price']]),

        ]);

        return $this->table
            ->request($request)
            ->persistColumnsInCookie();
    },
]);
