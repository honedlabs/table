<?php

namespace Workbench\App\Tables;

use Honed\Core\Options\Option;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Actions\PageAction;
use Honed\Table\Columns\BooleanColumn;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\DateColumn;
use Honed\Table\Columns\NumericColumn;
use Honed\Table\Columns\TextColumn;
use Honed\Table\Filters\BooleanFilter;
use Honed\Table\Filters\DateFilter;
use Honed\Table\Filters\Filter;
use Honed\Table\Filters\QueryFilter;
use Honed\Table\Filters\SelectFilter;
use Honed\Table\Sorts\Sort;
use Honed\Table\Table;
use Workbench\App\Models\Product;

final class ProductTable extends Table
{
    protected $resource = Product::class;

    protected $search = ['name', 'description'];

    protected $perPage = [10, 50, 100];

    protected $toggleable = true;

    protected function columns(): array
    {
        return [
            Column::make('id')->key()->hide(),
            // TextColumn::make('name')->sort(),
            // TextColumn::make('description')->fallback('No description')->sort(),
            // DateColumn::make('created_at')->format('d M Y'),
            // NumericColumn::make('price')->transform(fn ($value) => '$'.number_format($value, 2)),
            // BooleanColumn::make('best_seller', 'Favourite'),
            Column::make('misc')->fallback('N/A'),
        ];
    }

    protected function filters(): array
    {
        return [
            Filter::make('name')->like(),
            // BooleanFilter::make('best_seller', 'availability', 1),
            // DateFilter::make('created_at', 'before')->operator('<='),
            // SelectFilter::make('price', 'price-max')->options([
            //     Option::make(100),
            //     Option::make(500),
            //     Option::make(1000),
            //     Option::make(5000),
            // ]),
            // QueryFilter::make('id')->query(fn (Builder $builder, $value) => $builder->where('id', '<', $value)),
        ];
    }

    protected function sorts(): array
    {
        return [
            Sort::make('created_at', 'newest')->desc()->default(),
            Sort::make('created_at', 'oldest')->asc(),
        ];
    }

    protected function actions(): array
    {
        return [
            InlineAction::make('edit')
                ->action(fn (Product $product) => $product->update(['name' => 'Inline'])),
                
            InlineAction::make('delete')
                ->authorize(fn (Product $product) => true)
                ->action(fn (Product $product) => $product->delete()),

            BulkAction::make('Edit')->action(fn (Product $product) => $product->update(['name' => 'Bulk'])),
            BulkAction::make('Mass')->action(fn (Product $product) => $product->update(['name' => 'All'])),
            // PageAction::make('add')->label('Add User'),
            // BulkAction::make('delete')->label('Delete Users'),
        ];
    }
}
