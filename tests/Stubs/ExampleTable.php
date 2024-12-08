<?php

declare(strict_types=1);

namespace Honed\Table\Tests\Stubs;

use Honed\Table\Table;
use Honed\Table\Sorts\Sort;
use Honed\Table\Columns\Column;
use Honed\Table\Filters\Filter;
use Workbench\App\Enums\Status;
use Honed\Table\Confirm\Confirm;
use Honed\Table\Sorts\CustomSort;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Filters\SetFilter;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\PageAction;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Columns\NumberColumn;
use Honed\Table\Filters\CustomFilter;
use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Filters\DateFilter;
use Illuminate\Database\Eloquent\Builder;

class ExampleTable extends Table
{
    public $resource = Product::class;

    public $search = ['description'];

    public $toggleable = true;

    public $perPage = [10, 25, 50];

    public $defaultPerPage = 10;

    public $cookieName = 'example-table';

    public function columns()
    {
        return [
            Column::make('id')->key(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('description')->placeholder('-'), //->truncate(100)
            BooleanColumn::make('best_seller', 'Favourite'), //->labels('Favourite', 'Not Favourite'),
            Column::make('status')->meta(['badge' => true]),
            NumberColumn::make('price')->currency(),
            DateColumn::make('created_at')->sortable(),
        ];
    }

    public function filters()
    {
        return [
            Filter::make('price')->gt(),
            SetFilter::make('status')->options(Status::cases())->strict(),
            CustomFilter::make('soon')->using(fn (Builder $query, $value) => $query->where('status', Status::COMING_SOON)),
            DateFilter::make('created_at', 'Year')->alias('year')->year()
        ];
    }

    public function sorts()
    {
        return [
            Sort::make('name', 'A-Z')->asc(),
            Sort::make('name', 'Z-A')->desc(),
            CustomSort::make('desc')->using(fn (Builder $query) => $query->orderByDesc('description')),
        ];
    }

    public function actions()
    {
        return [
            InlineAction::make('edit')
                ->action(fn (Product $product) => $product->update(['name' => 'Inline']))
                ->bulk(),

            InlineAction::make('delete')
                ->authorize(fn (Product $product) => $product->id % 2 === 0)
                ->action(fn (Product $product) => $product->delete())
                ->confirm(fn (Confirm $confirm) => $confirm->title(fn (Product $product) => 'You are about to delete ' . $product->name)->description('Are you sure?')),

            InlineAction::make('edit-route')
                ->route('product.show'),

            BulkAction::make('touch')->action(fn (Product $product) => $product->update(['name' => 'Bulk'])),

            PageAction::make('create')->url->to('/products/create'),

        ];
    }
}