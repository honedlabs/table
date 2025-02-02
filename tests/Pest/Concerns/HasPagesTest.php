<?php

declare(strict_types=1);

use Honed\Table\PageAmount;
use Honed\Table\Concerns\HasPages;
use Illuminate\Support\Collection;
use Honed\Table\Tests\Stubs\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\CursorPaginator;
use Honed\Table\Exceptions\InvalidPaginatorException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class HasPagesTest
{
    use HasPages;
}

class HasPagesMethodTest extends HasPagesTest
{
    public function page()
    {
        return 'page';
    }

    public function paginator()
    {
        return 'length-aware';
    }

    public function shown()
    {
        return 'records';
    }

    public function perPage()
    {
        return [10, 20, 50];
    }

    public function defaultPerPage()
    {
        return 20;
    }
    
}

beforeEach(function () {
    HasPagesTest::usePageKey();
    HasPagesTest::usePaginator();
    HasPagesTest::recordsPerPage();
    HasPagesTest::useShownKey();
    $this->test = new HasPagesTest();
    $this->method = new HasPagesMethodTest();
});

it('configures page key', function () {
    HasPagesTest::usePageKey('p');
    expect($this->test->getPageKey())
        ->toBe('p');

    expect($this->method->getPageKey())
        ->toBe('page');
});

it('configures shown key', function () {
    HasPagesTest::useShownKey('num');
    expect($this->test->getShownKey())
        ->toBe('num');

    expect($this->method->getShownKey())
        ->toBe('records');
});

it('configures records per page', function () {
    HasPagesTest::recordsPerPage([5, 10, 20]);
    expect($this->test->getPerPage())
        ->toBe([5, 10, 20]);

    expect($this->method->getPerPage())
        ->toBe([10, 20, 50]);
});

it('configures paginator', function () {
    HasPagesTest::usePaginator('cursor');
    expect($this->test->getPaginator())
        ->toBe('cursor');

    expect($this->method->getPaginator())
        ->toBe('length-aware');
});

it('retrieves per page', function () {
    expect($this->test)
        ->getPerPage()->toBe($this->test->getDefaultPerPage());
    
    expect($this->method)
        ->getPerPage()->toBe([10, 20, 50]);
});

it('retrieves default per page', function () {
    expect($this->test)
        ->getDefaultPerPage()->toBe(10);
    
    expect($this->method)
        ->getDefaultPerPage()->toBe(20);
});

it('retrieves paginator', function () {
    expect($this->test)
        ->getPaginator()->toBe(LengthAwarePaginator::class);
    
    expect($this->method)
        ->getPaginator()->toBe('length-aware');
});

it('sets paginator', function () {
    $this->test->setPaginator('simple');

    expect($this->test)
        ->getPaginator()->toBe('simple');
});

it('retrieves page key', function () {
    expect($this->test)
        ->getPageKey()->toBeNull();
    
    expect($this->method)
        ->getPageKey()->toBe('page');
});

it('retrieves shown key', function () {
    expect($this->test)
        ->getShownKey()->toBe('show');
    
    expect($this->method)
        ->getShownKey()->toBe('records');
});

it('sets pages', function () {
    $this->test->setPages(collect([PageAmount::make(10), PageAmount::make(20)]));

    expect($this->test)
        ->hasPages()->toBeTrue()
        ->getPages()->scoped(fn ($pages) => $pages
            ->toHaveCount(2)
            ->sequence(
                fn ($page) => $page
                    ->getValue()->toBe(10)
                    ->isActive()->toBeFalse(),
                fn ($page) => $page
                    ->getValue()->toBe(20)
                    ->isActive()->toBeFalse(),
            )
        );
});

it('gets number of records from empty request', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
        'other' => 10
    ]);

    expect($this->method)
        ->getRecordsPerPage($request)->toBe(20)
        ->getPages()->scoped(fn ($pages) => $pages
            ->toHaveCount(3)
            ->sequence(
                fn ($page) => $page
                    ->getValue()->toBe(10)
                    ->isActive()->toBeFalse(),
                fn ($page) => $page
                    ->getValue()->toBe(20)
                    ->isActive()->toBeTrue(),
                fn ($page) => $page
                    ->getValue()->toBe(50)
                    ->isActive()->toBeFalse(),
            )
        );
});

it('gets number of records when not array', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
        $this->test->getShownKey() => 100
    ]);

    expect($this->test)
        ->getRecordsPerPage($request)->toBe(10)
        ->hasPages()->toBeFalse()
        ->getPages()->toBeNull();
});

it('gets number of records from request', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
        $this->method->getShownKey() => 50
    ]);

    expect($this->method)
        ->getRecordsPerPage($request)->toBe(50)
        ->getPages()->scoped(fn ($pages) => $pages
            ->toHaveCount(3)
            ->sequence(
                fn ($page) => $page
                    ->getValue()->toBe(10)
                    ->isActive()->toBeFalse(),
                fn ($page) => $page
                    ->getValue()->toBe(20)
                    ->isActive()->toBeFalse(),
                fn ($page) => $page
                    ->getValue()->toBe(50)
                    ->isActive()->toBeTrue(),
            )
        );
});

it('prevents invalid page amounts', function () {
    $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
        $this->method->getShownKey() => 100
    ]);

    expect($this->method)
        ->getRecordsPerPage($request)->toBe(20)
        ->getPages()->scoped(fn ($pages) => $pages
            ->toHaveCount(3)
            ->sequence(
                fn ($page) => $page
                    ->getValue()->toBe(10)
                    ->isActive()->toBeFalse(),
                fn ($page) => $page
                    ->getValue()->toBe(20)
                    ->isActive()->toBeTrue(),
                fn ($page) => $page
                    ->getValue()->toBe(50)
                    ->isActive()->toBeFalse(),
            )
        );
});

describe('paginates', function () {

    beforeEach(function () {
        foreach (\range(1, 20) as $i) {
            product();
        }
    });

    it('paginates length aware', function () {
        $builder = Product::query();

        $builder = Product::query();

        expect($this->test->paginateRecords($builder))
            ->toBeInstanceOf(LengthAwarePaginator::class)
            ->toHaveCount(10);
    });

    it('paginates simple', function () {
        $this->test->setPaginator('simple');

        $builder = Product::query();

        expect($this->test->paginateRecords($builder))
            ->toBeInstanceOf(Paginator::class)
            ->toHaveCount(10);
    });

    it('paginates cursor', function () {
        $this->test->setPaginator('cursor');

        $builder = Product::query();

        expect($this->test->paginateRecords($builder))
            ->toBeInstanceOf(CursorPaginator::class)
            ->toHaveCount(10);
    });

    it('paginates collection', function () {
        $this->test->setPaginator('collection');

        $builder = Product::query();

        expect($this->test->paginateRecords($builder))
            ->toBeInstanceOf(Collection::class)
            ->toHaveCount(20);
    });

    it('errors on invalid paginator', function () {
        $this->test->setPaginator('invalid');

        $builder = Product::query();

        $this->test->paginateRecords($builder);
    })->throws(InvalidPaginatorException::class);

    it('handles pipeline correctly', function () {
        $request = Request::create('/', HttpFoundationRequest::METHOD_GET, [
            $this->method->getShownKey() => 20
        ]);

        $builder = Product::query();

        expect($this->method->paginateRecords($builder, $request))
            ->toBeInstanceOf(LengthAwarePaginator::class)
            ->toHaveCount(20);
    });
});
