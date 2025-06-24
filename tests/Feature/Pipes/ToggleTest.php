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
        ->toggleable()
        ->columns([
            Column::make('id')
                ->always(),

            Column::make('name')
                ->toggleable(false),

            Column::make('description')
                ->defaultToggled(),

            Column::make('price'),
        ]);
});

it('uses defaults', function () {
    $this->pipe->run(
        $this->table
    );

    expect($this->table->getHeadings())
        ->toHaveCount(3);

    expect(array_map(fn ($column) => $column->getName(), $this->table->getHeadings()))
        ->toEqual(['id', 'name', 'description']);

    expect(getColumn($this->table, 'id'))
        ->isAlways()->toBeTrue()
        ->isToggleable()->toBeTrue()
        ->isActive()->toBeFalse()
        ->isHidden()->toBeTrue();

    expect(getColumn($this->table, 'name'))
        ->isAlways()->toBeFalse()
        ->isToggleable()->toBeFalse()
        ->isActive()->toBeTrue()
        ->isHidden()->toBeFalse();

    expect(getColumn($this->table, 'description'))
        ->isAlways()->toBeFalse()
        ->isDefaultToggled()->toBeTrue()
        ->isActive()->toBeTrue()
        ->isHidden()->toBeFalse();

    expect(getColumn($this->table, 'price'))
        ->isAlways()->toBeFalse()
        ->isToggleable()->toBeTrue()
        ->isActive()->toBeFalse()
        ->isHidden()->toBeFalse();
})->with([
    'basic' => fn () => $this->table,

    'clear' => function () {
        $request = Request::create('/', 'GET', [
            $this->table->getColumnKey() => null,
        ]);

        return $this->table->request($request);
    },
]);

it('does not toggle if not toggleable', function () {
    $this->pipe->run(
        $this->table->toggleable(false)
    );

    expect($this->table->getHeadings())
        ->toHaveCount(4)
        ->each(fn ($column) => $column->isActive()->toBeTrue());
});

it('retrieves from sources', function ($table) {
    $this->pipe->run($table);

    expect($table->getHeadings())
        ->toHaveCount(3);

    expect(getColumn($table, 'id'))
        ->isAlways()->toBeTrue()
        ->isToggleable()->toBeTrue()
        ->isActive()->toBeFalse()
        ->isHidden()->toBeTrue();

    expect(getColumn($table, 'name'))
        ->isAlways()->toBeFalse()
        ->isToggleable()->toBeFalse()
        ->isActive()->toBeTrue()
        ->isHidden()->toBeFalse();

    expect(getColumn($table, 'description'))
        ->isAlways()->toBeFalse()
        ->isDefaultToggled()->toBeTrue()
        ->isActive()->toBeFalse()
        ->isHidden()->toBeFalse();

    expect(getColumn($table, 'price'))
        ->isAlways()->toBeFalse()
        ->isToggleable()->toBeTrue()
        ->isActive()->toBeTrue()
        ->isHidden()->toBeFalse();
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
