<?php

use Workbench\App\Models\Product;
use Conquest\Table\Actions\Action;
use Conquest\Table\Actions\BulkAction;
use Conquest\Table\Filters\Filter;
use Illuminate\Support\Facades\DB;
use Conquest\Table\Filters\Enums\Clause;
use Conquest\Table\Filters\Enums\Operator;
use Illuminate\Http\Request;

it('can create a row action', function () {
    $action = new Action($l = 'Create');
    expect($action->getLabel())->toBe($l);
    expect($action->getName())->toBe('create');
    expect($action->isAuthorised())->toBeTrue();
    expect($action->getResolvedRoute())->toBeNull();
    expect($action->getMethod())->toBeNull();
    expect($action->getType())->toBe('action:row');
    expect($action->hasHandler())->toBeFalse();
    expect($action->getMetadata())->toBe([]);
});

// it('can create a bulk action', function () {
//     $action = new BulkAction($l = 'Delete');
//     expect($action->getLabel())->toBe($l);
//     expect($action->getName())->toBe('delete');
//     expect($action->isAuthorised())->toBeTrue();
//     expect($action->getResolvedRoute())->toBeNull();
//     expect($action->getMethod())->toBe(Request::METHOD_POST);
//     expect($action->getType())->toBe('action:bulk');
//     expect($action->hasHandler())->toBeFalse();
//     expect($action->getMetadata())->toBe([]);
// });

// it('can create a filter with arguments', function () {
//     $filter = new Filter('name', 
//         name: 'username',
//         authorize: fn () => false,
//         clause: Clause::IS_NOT,
//         operator: Operator::NOT_EQUAL,
//         negate: true,
//     );

//     expect($filter->getProperty())->toBe('name');
//     expect($filter->getName())->toBe('username');
//     expect($filter->getLabel())->toBe('Username');
//     expect($filter->isAuthorised())->toBeFalse();
//     expect($filter->getClause())->toBe(Clause::IS_NOT);
//     expect($filter->getOperator())->toBe(Operator::NOT_EQUAL);
//     expect($filter->isNegated())->toBeTrue();
// });

// it('can make a filter', function () {
//     $filter = Filter::make('name');
//     expect($filter->getProperty())->toBe('name');
//     expect($filter->getLabel())->toBe('Name');
// });

// it('can chain methods on a filter', function () {
//     $filter = Filter::make('name')
//         ->name('username')
//         ->authorize(fn () => false)
//         ->clause(Clause::IS_NOT)
//         ->operator(Operator::NOT_EQUAL)
//         ->negate();
    
//     expect($filter->getProperty())->toBe('name');
//     expect($filter->getName())->toBe('username');
//     expect($filter->getLabel())->toBe('Name'); // Uses property to generate label
//     expect($filter->isAuthorised())->toBeFalse();
//     expect($filter->getClause())->toBe(Clause::IS_NOT);
//     expect($filter->getOperator())->toBe(Operator::NOT_EQUAL);
//     expect($filter->isNegated())->toBeTrue();
// });

// it('can apply a filter to an eloquent builder', function () {
//     $filter = Filter::make('name');
//     $builder = Product::query();
//     // Requires a request to work
//     request()->merge(['name' => 'test']);
//     $filter->apply($builder);
//     expect($builder->toSql())->toBe('select * from "products" where "name" = ?');
// });

// it('can apply a filter to a query builder', function () {
//     $filter = Filter::make('name');
//     $builder = DB::table('products');
//     request()->merge(['name' => 'test']);
//     $filter->apply($builder);
//     expect($builder->toSql())->toBe('select * from "products" where "name" = ?');
// });
