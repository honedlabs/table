<?php

use Honed\Table\Concerns\Sortable;
use Honed\Table\Sorts\Sort;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class SortableTest
{
    use Sortable;

    protected $sorts;
}

class SortableMethodTest extends SortableTest
{
    protected $sort = 's';

    protected $order = 'o';

    public function sorts(): array
    {
        return [
            Sort::make('test'),
        ];
    }
}

beforeEach(function () {
    SortableTest::useSortKey();
    SortableTest::useOrderKey();

    $this->test = new SortableTest;
    $this->method = new SortableMethodTest;
    Request::swap(
        Request::create('/', HttpFoundationRequest::METHOD_GET, [
            SortableTest::SortKey => 'test',
            SortableTest::OrderKey => 'asc',
        ])
    );
});

it('configures a sort key', function () {
    SortableTest::useSortKey('example');
    expect($this->test->getSortKey())->toBe('example');
});

it('configures a order key', function () {
    SortableTest::useOrderKey('example');
    expect($this->test->getOrderKey())->toBe('example');
});

it('retrieves sort key', function () {
    expect($this->method->getSortKey())->toBe('s');
});

it('retrieves order key', function () {
    expect($this->method->getOrderKey())->toBe('o');
});

it('is empty by default', function () {
    expect($this->test)
        ->hasSorts()->toBeFalse();

    expect($this->method)
        ->hasSorts()->toBeTrue()
        ->getSorts()->toHaveCount(1);
});

it('sets sorts', function () {
    $this->method->setSorts([Sort::make('test')]);

    expect($this->method)
        ->hasSorts()->toBeTrue()
        ->getSorts()->scoped(fn ($sorts) => $sorts
        ->toBeCollection()
        ->toHaveCount(1)
        ->first()->scoped(fn ($sort) => $sort
        ->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('test')
        )
        );
});

it('rejects null values', function () {
    $this->method->setSorts([Sort::make('test')]);
    $this->method->setSorts(null);

    expect($this->method)
        ->hasSorts()->toBeTrue()
        ->getSorts()->toHaveCount(1);
});

it('retrieves sorts from method', function () {
    expect($this->method)
        ->hasSorts()->toBeTrue()
        ->getSorts()->scoped(fn ($sorts) => $sorts
        ->toBeCollection()
        ->toHaveCount(1)
        ->first()->scoped(fn ($sort) => $sort
        ->toBeInstanceOf(Sort::class)
        ->getAttribute()->toBe('test')
        )
        );
});

it('applies sorts', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
        $this->method->getSortKey() => 'test',
        $this->method->getOrderKey() => 'asc',
    ]);

    $builder = Product::query();

    $this->method->sortQuery($builder, $request);

    expect($builder->getQuery()->orders)
        ->toHaveCount(1)
        ->toEqual([
            [
                'column' => 'test',
                'direction' => 'asc',
            ],
        ]);
});

it('applies sort with direction on parameter', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
        $this->method->getSortKey() => '-test',
        $this->method->getOrderKey() => 'asc',
    ]);

    $builder = Product::query();

    $this->method->sortQuery($builder, $request);

    expect($builder->getQuery()->orders)
        ->toHaveCount(1)
        ->toEqual([
            [
                'column' => 'test',
                'direction' => 'desc',
            ],
        ]);
});

it('does not apply sort if no match', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
        $this->method->getSortKey() => 'other',
        $this->method->getOrderKey() => 'asc',
    ]);

    $builder = Product::query();

    $this->method->sortQuery($builder, $request);

    expect($builder->getQuery()->orders)->toBeEmpty();
});
