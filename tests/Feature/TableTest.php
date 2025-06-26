<?php

declare(strict_types=1);

use Honed\Table\Columns\KeyColumn;
use Honed\Table\EmptyState;
use Honed\Table\Exceptions\KeyNotFoundException;
use Honed\Table\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Workbench\App\Models\Product;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    $this->table = Table::make()
        ->for(Product::class)
        ->key('id');
});

afterEach(function () {
    Table::flushState();
});

it('has key', function () {
    expect($this->table)
        ->columns(KeyColumn::make('id'))
        ->getKey()->toBe('id')
        ->key('test')->toBe($this->table)
        ->getKey()->toBe('test');
});

it('requires key', function () {
    $this->table->key(null)->getKey();
})->throws(KeyNotFoundException::class);

it('is url routable', function () {
    expect($this->table)
        ->getRouteKeyName()->toBe('table');

    expect($this->table)
        ->resolveRouteBinding($this->table->getRouteKey())
        ->toBeNull();

    $table = ProductTable::make();

    expect($table)
        ->resolveRouteBinding($table->getRouteKey())
        ->toBeInstanceOf(ProductTable::class);

    expect($table)
        ->resolveChildRouteBinding(null, $table->getRouteKey())
        ->toBeInstanceOf(ProductTable::class);
});

it('resolves table', function () {
    ProductTable::guessTableNamesUsing(function ($class) {
        return Str::of($class)
            ->classBasename()
            ->prepend('Workbench\\App\\Tables\\')
            ->append('Table')
            ->value();
    });

    expect(ProductTable::resolveTableName(Product::class))
        ->toBe(ProductTable::class);

    expect(ProductTable::tableForModel(Product::class))
        ->toBeInstanceOf(ProductTable::class);
});

it('uses namespace', function () {
    ProductTable::useNamespace('');

    expect(ProductTable::resolveTableName(Product::class))
        ->toBe(Str::of(ProductTable::class)
            ->classBasename()
            ->prepend('Models\\')
            ->value()
        );
});

it('has array representation', function () {
    expect($this->table->toArray())
        ->toBeArray()
        ->toHaveKeys([
            'sort',
            'search',
            'delimiter',
            'sorts',
            'filters',
            'searches',
            'key',
            'page',
            'records',
            'paginate',
            'columns',
            'toggleable',
            'pages',
            'operations',
            'emptyState',
        ])
        ->not->toHaveKeys([
            'column',
            'record',
            'match',
            'term',
            'placeholder',
            'views',
            'meta',
        ])
        ->{'sort'}->toBe($this->table->getSortKey())
        ->{'search'}->toBe($this->table->getSearchKey())
        ->{'delimiter'}->toBe($this->table->getDelimiter())
        ->{'sorts'}->toBeArray()
        ->{'filters'}->toBeArray()
        ->{'searches'}->toBeArray()
        ->{'key'}->toBe($this->table->getKey())
        ->{'page'}->toBe($this->table->getPageKey())
        ->{'records'}->toBeArray()
        ->{'paginate'}->toBeArray()
        ->{'paginate'}->toBeArray()
        ->{'columns'}->toBeArray()
        ->{'toggleable'}->toBeFalse()
        ->{'operations'}
        ->scoped(fn ($operations) => $operations
            ->toBeArray()
            ->toHaveCount(3)
            ->toHaveKeys([
                'inline',
                'bulk',
                'page',
            ])
            ->{'inline'}->toBeFalse()
            ->{'bulk'}->toBeArray()
            ->{'page'}->toBeArray()
        )
        ->{'toggleable'}->toBeFalse()
        ->{'pages'}->toBeArray()
        ->{'emptyState'}
        ->scoped(fn ($emptyState) => $emptyState
            ->toBeArray()
            ->toHaveKeys(['heading', 'description', 'operations'])
            ->not->toHaveKey('icon')
        );
});

it('serializes to json', function () {
    expect($this->table)
        ->jsonSerialize()->toEqual($this->table->toArray());
});

describe('evaluation', function () {
    it('has named dependencies', function ($closure, $class) {
        expect($this->table->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'emptyState' => fn () => [fn ($emptyState) => $emptyState, EmptyState::class],
        'builder' => fn () => [fn ($builder) => $builder, Builder::class],
        'query' => fn () => [fn ($query) => $query, Builder::class],
        'q' => fn () => [fn ($q) => $q, Builder::class],
        'request' => fn () => [fn ($request) => $request, Request::class],
        'table' => fn () => [fn ($table) => $table, Table::class],
    ]);

    it('has typed dependencies', function ($closure, $class) {
        expect($this->table->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        'emptyState' => fn () => [fn (EmptyState $arg) => $arg, EmptyState::class],
        'request' => fn () => [fn (Request $arg) => $arg, Request::class],
        'builder' => fn () => [fn (Builder $arg) => $arg, Builder::class],
        'builder contract' => fn () => [fn (BuilderContract $arg) => $arg, Builder::class],
        'table' => fn () => [fn (Table $arg) => $arg, Table::class],
    ]);
});
