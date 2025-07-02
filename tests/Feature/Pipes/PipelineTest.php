<?php

declare(strict_types=1);

use Honed\Table\Facades\Views;
use Illuminate\Http\Request;
use Workbench\App\Enums\Status;
use Workbench\App\Models\Product;
use Workbench\App\Tables\ProductTable;

beforeEach(function () {
    Product::factory()->count(100)->create();

    $this->table = ProductTable::make();

    $this->request = Request::create('/', 'GET', [
        'name' => 'test',

        'price' => 100,
        'status' => \sprintf('%s,%s', Status::Available->value, Status::Unavailable->value),
        'only' => Status::ComingSoon->value,

        'favourite' => '1',

        'oldest' => '2000-01-01',
        'newest' => '2001-01-01',

        'missing' => 'test',

        $this->table->getSortKey() => '-price',
        $this->table->getSearchKey() => 'search+term',
        $this->table->getColumnKey() => 'id,name,price,status,best_seller,created_at',
        $this->table->getRecordKey() => 25,
    ]);

    $this->table->request($this->request);

    Views::set($this->table::class, 'Filter view', null, [
        'name' => 'joshua',
    ]);
});

it('builds class', function () {
    expect($this->table->build()->getBuilder()->getQuery())
        ->wheres
        ->scoped(fn ($wheres) => $wheres
            ->toBeArray()
            ->toHaveCount(9)
            ->toEqualCanonicalizing([
                // Search done on name (column) and description (property)
                [
                    'type' => 'raw',
                    'sql' => 'LOWER(description) LIKE ?',
                    'boolean' => 'and',
                ],
                [
                    'type' => 'raw',
                    'sql' => 'LOWER(name) LIKE ?',
                    'boolean' => 'or',
                ],
                // Name where filter
                [
                    'type' => 'raw',
                    'sql' => 'LOWER(name) LIKE ?',
                    'boolean' => 'and',
                ],
                // Price set filter
                [
                    'type' => 'Basic',
                    'column' => 'price',
                    'operator' => '<=',
                    'value' => 100,
                    'boolean' => 'and',
                ],
                // Status set filter
                [
                    'type' => 'In',
                    'column' => 'status',
                    'values' => [Status::Available->value, Status::Unavailable->value],
                    'boolean' => 'and',
                ],
                // Only set filter
                [
                    'type' => 'In',
                    'column' => 'status',
                    'values' => [Status::ComingSoon->value],
                    'boolean' => 'and',
                ],
                // Favourite filter
                [
                    'type' => 'Basic',
                    'column' => 'best_seller',
                    'operator' => '=',
                    'value' => true,
                    'boolean' => 'and',
                ],
                // Oldest date filter
                [
                    'type' => 'Date',
                    'column' => 'created_at',
                    'operator' => '>=',
                    'value' => '2000-01-01',
                    'boolean' => 'and',
                ],
                // Newest date filter
                [
                    'type' => 'Date',
                    'column' => 'created_at',
                    'operator' => '<=',
                    'value' => '2001-01-01',
                    'boolean' => 'and',
                ],
            ])
        )
        ->orders
        ->scoped(fn ($orders) => $orders
            ->toBeArray()
            ->toHaveCount(1)
            ->{0}->toEqual([
                'column' => 'price',
                'direction' => 'desc',
            ])
        );

    expect($this->table)
        ->isSorting()->toBeTrue()
        ->isSearching()->toBeTrue()
        ->isFiltering()->toBeTrue()
        ->toArray()
        ->scoped(fn ($array) => $array
            ->toHaveKeys([
                '_id',
                '_column_key',
                '_record_key',
                '_page_key',
                '_search_key',
                '_sort_key',
                '_delimiter',
                'toggleable',
                'term',
                'sorts',
                'filters',
                'searches',
                'records',
                'paginate',
                'columns',
                'pages',
                'operations',
                'views',
                'state',
                'meta',
            ])
            ->not->toHaveKeys([
                '_match_key',
            ])
            ->{'_id'}->toBe($this->table->getId())
            ->{'_search_key'}->toBe($this->table->getSearchKey())
            ->{'_sort_key'}->toBe($this->table->getSortKey())
            ->{'_delimiter'}->toBe($this->table->getDelimiter())
            ->{'_column_key'}->toBe($this->table->getColumnKey())
            ->{'_record_key'}->toBe($this->table->getRecordKey())
            ->{'_page_key'}->toBe($this->table->getPageKey())
            ->{'term'}->toBe($this->table->getSearchTerm())
            ->{'sorts'}
            ->scoped(fn ($sorts) => $sorts
                ->toBeArray()
                // ->toHaveCount(count($this->table->getSorts()))
            )
            ->{'filters'}
            ->scoped(fn ($filters) => $filters
                ->toBeArray()
                // ->toHaveCount(count($this->table->getFilters()))
            )
            ->{'searches'}
            ->scoped(fn ($searches) => $searches
                ->toBeArray()
                ->toBeEmpty()
            )
            ->{'records'}
            ->scoped(fn ($records) => $records
                ->toBeArray()
                ->toBeEmpty() // no records matching the given filters
            )
            ->{'paginate'}
            ->scoped(fn ($paginate) => $paginate
                ->toBeArray()
                ->{'empty'}->toBeTrue()
            )
            ->{'columns'}
            ->scoped(fn ($columns) => $columns
                ->toBeArray()
                ->toHaveCount(count($this->table->getColumns()))
            )
            ->{'pages'}
            ->scoped(fn ($pages) => $pages
                ->toBeArray()
                ->toHaveCount(count($this->table->getPerPage()))
            )
            ->{'toggleable'}->toBeTrue()
            ->{'operations'}
            ->scoped(fn ($operations) => $operations
                ->toBeArray()
                ->toHaveKeys(['inline', 'bulk', 'page'])
                ->{'inline'}->toBeTrue()
                ->{'bulk'}
                ->scoped(fn ($bulk) => $bulk
                    ->toBeArray()
                    ->toHaveCount(count($this->table->getBulkOperations()))
                )
                ->{'page'}
                ->scoped(fn ($page) => $page
                    ->toBeArray()
                    ->toHaveCount(count($this->table->getPageOperations()))
                )
            )
            ->{'views'}
            ->scoped(fn ($views) => $views
                ->toBeArray()
                ->toHaveCount(1)
                ->{0}
                ->scoped(fn ($view) => $view
                    ->toBeObject()
                    ->id->toBe(1)
                    ->name->toBe('Filter view')
                )
            )
            ->{'state'}
            ->scoped(fn ($emptyState) => $emptyState
                ->toBeArray()
                ->toHaveKeys(['heading', 'description', 'operations'])
                ->not->toHaveKey('icon')
            )
            ->{'meta'}->toBe([])
        );
});
