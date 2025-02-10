<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Fixtures;

use Honed\Action\BulkAction;
use Honed\Action\Confirm;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Refine\Filters\DateFilter;
use Honed\Refine\Filters\Filter;
use Honed\Refine\Filters\SetFilter;
use Honed\Refine\Sorts\Sort;
use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\NumericColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Table as HonedTable;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\Status;

class Table extends HonedTable
{
    public $search = ['description'];

    public $toggle = true;

    public $pagination = [10, 25, 50];

    public $defaultPagination = 10;

    public $cookie = 'example-table';

    public function resource()
    {
        return Product::query()
            ->with(['seller', 'categories']);
    }

    public function columns()
    {
        return [
            Column::make('id')->key(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('description')->placeholder('-'), // ->truncate(100)
            BooleanColumn::make('best_seller', 'Favourite')->formatBoolean('Favourite', 'Not favourite'),
            TextColumn::make('seller.name', 'Sold by'),
            Column::make('status')->meta(['badge' => true]),
            NumericColumn::make('price'),
            DateColumn::make('created_at')->sortable(),
        ];
    }

    public function filters()
    {
        return [
            Filter::make('price', 'Max')->alias('max')->lt(),
            Filter::make('price', 'Min')->alias('min')->gt(),
            SetFilter::make('status')->options(Status::class),
            DateFilter::make('created_at', 'Year')->alias('year'),
        ];
    }

    public function sorts()
    {
        return [
            Sort::make('name', 'A-Z')->asc(),
            Sort::make('name', 'Z-A')->desc(),
        ];
    }

    public function actions()
    {
        return [
            InlineAction::make('edit')
                ->action(fn (Product $product) => $product->update(['name' => 'Inline'])),
            InlineAction::make('delete')
                ->allow(fn (Product $product) => $product->id % 2 === 0)
                ->action(fn (Product $product) => $product->delete())
                ->confirm(fn (Confirm $confirm) => $confirm->name(fn (Product $product) => 'You are about to delete '.$product->name)->description('Are you sure?')),
            InlineAction::make('show')
                ->route(fn ($product) => route('products.show', $product)),
            BulkAction::make('update')
                ->action(fn (Product $product) => $product->update(['name' => 'Bulk'])),
            PageAction::make('create')
                ->route('products.create'),
        ];
    }
}
