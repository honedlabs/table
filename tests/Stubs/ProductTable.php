<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Stubs;

use Honed\Action\BulkAction;
use Honed\Action\InlineAction;
use Honed\Action\PageAction;
use Honed\Refine\Filter;
use Honed\Refine\Search;
use Honed\Refine\Sort;
use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\HiddenColumn;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\Columns\NumberColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Table;

class ProductTable extends Table
{
    protected $toggle = true;

    protected $remember = true;

    protected $pagination = [10, 25, 50];

    /**
     * {@inheritdoc}
     */
    public function defineResource()
    {
        return Product::query()
            ->with(['seller', 'categories']);
    }

    /**
     * {@inheritdoc}
     */
    public function defineColumns()
    {
        return [
            KeyColumn::make('id'),

            TextColumn::make('name')
                ->always()
                ->searches(),

            TextColumn::make('description')
                ->filters()
                ->fallback('-'),

            BooleanColumn::make('best_seller', 'Favourite')
                ->labels('Favourite', 'Not favourite'),

            TextColumn::make('seller.name', 'Sold by')
                ->sometimes(),

            Column::make('status'),

            NumberColumn::make('price')
                ->alias('cost')
                ->sorts(),

            DateColumn::make('created_at')
                ->sometimes()
                ->sorts(),

            HiddenColumn::make('public_id')
                ->hidden()
                ->always(),

            DateColumn::make('updated_at')
                ->filters()
                ->allow(false),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defineFilters()
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
                ->alias('only')
                ->enum(Status::class),

            Filter::make('best_seller', 'Favourite')
                ->boolean()
                ->alias('favourite'),

            Filter::make('created_at', 'Oldest')
                ->alias('oldest')
                ->date()
                ->operator('>='),

            Filter::make('created_at', 'Newest')
                ->alias('newest')
                ->operator('<=')
                ->asDate(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defineSorts()
    {
        return [
            Sort::make('name', 'A-Z')
                ->desc()
                ->default(),

            Sort::make('name', 'Z-A')
                ->asc(),

            Sort::make('price'),

            Sort::make('best_seller', 'Favourite')
                ->alias('favourite'),

            Sort::make('updated_at')
                ->allow(false),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defineSearches()
    {
        return [
            Search::make('description'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defineActions()
    {
        return [
            InlineAction::make('edit')
                ->action(fn ($product) => $product->update(['name' => 'Inline'])),

            InlineAction::make('delete')
                ->allow(fn ($product) => $product->id % 2 === 0)
                ->action(fn ($product) => $product->delete())
                ->confirm(fn ($confirm) => $confirm
                    ->title(fn ($product) => 'You are about to delete '.$product->name)
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
