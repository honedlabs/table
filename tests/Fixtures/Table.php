<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Fixtures;

use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Refine\Filter;
use Honed\Refine\Search;
use Honed\Refine\Sort;
use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\Columns\NumberColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Table as HonedTable;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\Status;

class Table extends HonedTable
{
    const Endpoint = '/test';

    const Delimiter = '|';

    const Paginator = 'simple';

    const Pagination = [10, 25, 50];

    const DefaultPagination = 25;

    const PagesKey = 'p';

    const RecordsKey = 'records';

    const SearchesKey = 's';

    const SortsKey = 'order';

    const Search = ['description'];

    const Toggle = true;

    const Remember = true;

    const ColumnsKey = 'cols';

    const Duration = 10;

    const CookieName = 'example-table';

    public $endpoint = self::Endpoint;

    public $delimiter = self::Delimiter;

    public $pagination = self::Pagination;

    public $paginator = self::Paginator;

    public $defaultPagination = self::DefaultPagination;

    public $pagesKey = self::PagesKey;

    public $recordsKey = self::RecordsKey;

    public $searchesKey = self::SearchesKey;

    public $sortsKey = self::SortsKey;

    public $toggle = self::Toggle;

    public $remember = self::Remember;

    public $columnsKey = self::ColumnsKey;

    public $duration = self::Duration;

    public $cookieName = self::CookieName;

    public function for()
    {
        return Product::query()
            ->with(['seller', 'categories']);
    }

    public function columns()
    {
        return [
            KeyColumn::make('id'),

            TextColumn::make('name')
                ->always()
                ->searchable(),

            TextColumn::make('description')
                ->fallback('-'),

            BooleanColumn::make('best_seller', 'Favourite')
                ->labels('Favourite', 'Not favourite'),

            TextColumn::make('seller.name', 'Sold by')
                ->sometimes(),

            Column::make('status')
                ->meta(['badge' => true]),

            NumberColumn::make('price')
                ->sortable(),

            DateColumn::make('created_at')
                ->sometimes()
                ->sortable(),

            Column::make('public_id')
                ->hidden()
                ->always(),

            Column::make('updated_at')
                ->allow(false),
        ];
    }

    public function filters()
    {
        return [
            Filter::make('name')->operator('like'),

            Filter::make('price', 'Maximum price')
                ->strict()
                ->operator('<=')
                ->options([10, 20, 50, 100]),

            Filter::make('status')
                ->enum(Status::class)
                ->multiple(),

            Filter::make('status', 'Single')
                ->alias('only')->enum(Status::class),

            Filter::make('best_seller', 'Favourite')
                ->asBoolean()
                ->alias('favourite'),

            Filter::make('created_at', 'Oldest')
                ->alias('oldest')
                ->operator('>=')
                ->asDate(),

            Filter::make('created_at', 'Newest')
                ->alias('newest')
                ->operator('<=')
                ->asDate(),
        ];
    }

    public function sorts()
    {
        return [
            Sort::make('name', 'A-Z')
                ->alias('name-desc')
                ->desc()
                ->default(),

            Sort::make('name', 'Z-A')
                ->alias('name-asc')
                ->asc(),

            Sort::make('price'),

            Sort::make('best_seller', 'Favourite')
                ->alias('favourite'),

            Sort::make('updated_at')
                ->allow(false),
        ];
    }

    public function searches()
    {
        return [
            Search::make('description'),
        ];
    }

    public function actions()
    {
        return [
            InlineAction::make('edit')
                ->action(fn ($product) => $product->update(['name' => 'Inline'])),

            InlineAction::make('delete')
                ->allow(fn ($product) => $product->id % 2 === 0)
                ->action(fn ($product) => $product->delete())
                ->confirm(fn ($confirm) => $confirm
                    ->label(fn ($product) => 'You are about to delete '.$product->name)
                    ->description('Are you sure?')),

            InlineAction::make('show')
                ->route(fn ($product) => route('products.show', $product)),

            BulkAction::make('edit')
                ->action(fn ($product) => $product->update(['name' => 'Bulk'])),

            BulkAction::make('delete')
                ->action(fn ($product) => $product->delete())
                ->allow(false),

            PageAction::make('create')
                ->route('products.create'),

            PageAction::make('factory')
                ->action(function () {
                    $product = product('test');

                    return to_route('products.show', $product);
                }),
        ];
    }
}
