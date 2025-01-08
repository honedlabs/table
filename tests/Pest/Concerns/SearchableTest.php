<?php

declare(strict_types=1);

use function Pest\Laravel\get;
use Honed\Table\Columns\Column;
use Honed\Table\Concerns\Searchable;
use Honed\Table\Tests\Stubs\Product;

use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class SearchableTest
{
    use Searchable;
}

class SearchableMethodTest extends SearchableTest
{
    public $term = 'q';

    public function search()
    {
       return ['name', 'description'];
    }
}

beforeEach(function () {
    SearchableTest::useScout(false);
    SearchableTest::useSearchTerm();
    $this->test = new SearchableTest();
    $this->method = new SearchableMethodTest();
    Request::swap(Request::create('/', HttpFoundationRequest::METHOD_GET, [
        $this->method->getSearchTerm() => 'helloworld'
    ]));
});

it('configures a search name', function () {
    SearchableTest::useSearchTerm('test');
    expect($this->test)
        ->getSearchTerm()->toBe('test');
});

it('configures scout usage', function () {
    SearchableTest::useScout(true);
    expect($this->test)
        ->isScoutSearch()->toBeTrue();
});

it('retrieves search term', function () {
    expect($this->method)
        ->getSearchTerm()->toBe('q');
});

it('retrieves search columns', function () {
    expect($this->test->getSearch())
        ->toBeCollection()
        ->toBeEmpty();

    expect($this->method->getSearch())
        ->toBeCollection()
        ->toHaveCount(2);
});

it('retrieves search parameters from request', function () {
    expect($this->method)
        ->getSearchParameters()->toBe('helloworld');
});

it('determines whether to apply searching', function () {
    expect($this->method)
        ->isSearching()->toBeTrue();

    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
        'none' => 'helloworld'
    ]);

    expect($this->method)
        ->isSearching($request)->toBeFalse();
});

it('applies search to builder', function () {
    $builder = Product::query();

    $this->method->searchQuery($builder);

    expect($builder->getQuery()->wheres)
        ->toHaveCount(1)
        ->{0}->{'query'}->wheres
            ->toEqual([
                [
                    'type' => 'Basic',
                    'column' => 'name',
                    'value' => '%helloworld%',
                    'operator' => 'LIKE',
                    'boolean' => 'or',
                ],
                [
                    'type' => 'Basic',
                    'column' => 'description',
                    'value' => '%helloworld%',
                    'operator' => 'LIKE',
                    'boolean' => 'or',
                ]
            ]);
});


describe('searches', function () {
    beforeEach(function () {
        $this->columns = collect([
            Column::make('status')->searchable(),
            Column::make('price')->searchable()
        ]);
    });

    test('not by default', function () {
        $builder = Product::query();
    
        $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
            'none' => 'helloworld'
        ]);
    
        $this->method->searchQuery($builder, $this->columns, $request);
    
        expect($builder->getQuery()->wheres)
            ->toBeEmpty();
    });

    test('with request', function () {
        $builder = Product::query();
    
        $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
            $this->method->getSearchTerm() => 'helloworld'
        ]);
    
        $this->method->searchQuery($builder, $this->columns, $request);
    
        expect($builder->getQuery()->wheres)
            ->toHaveCount(1)
            ->{0}->scoped(fn ($nested) => $nested
                ->{'type'}->toBe('Nested')
                ->{'query'}->wheres
                    ->toHaveCount(4)
                    ->each(fn ($where) => $where
                        ->toBeArray()
                        ->toHaveKeys(['value', 'type', 'column', 'operator', 'boolean'])
                    )
            );
    });

    test('with scout', function () {
        SearchableMethodTest::useScout(true);

        foreach (range(1, 10) as $i) {
            product('helloworld');
        }

        $builder = Product::query();
    
        $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
            $this->method->getSearchTerm() => 'helloworld'
        ]);
    
        $this->method->searchQuery($builder, $this->columns, $request);
    
        expect($builder->getQuery()->wheres)
            ->toHaveCount(1)
            ->{0}->scoped(fn ($nested) => $nested
                ->{'type'}->toBe('In')
                ->{'column'}->toBe('id')
                ->{'values'}->toBe([])
                ->{'boolean'}->toBe('and')
            );
    });
});

