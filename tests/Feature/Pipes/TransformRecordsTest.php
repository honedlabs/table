<?php

declare(strict_types=1);

use Honed\Action\Actions\DestroyAction;
use Honed\Action\Operations\InlineOperation;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\Pipes\TransformRecords;
use Honed\Table\Table;
use Workbench\App\Enums\Status;
use Workbench\App\Models\Product;

beforeEach(function () {
    Product::factory()->count(100)->create();

    $this->pipe = new TransformRecords();

    $this->table = Table::make()
        ->operations([
            InlineOperation::make('view')
                ->url('products.show', '{id}'),

            InlineOperation::make('edit')
                ->allow(fn ($record) => $record->id % 2 === 0),

            InlineOperation::make('delete')
                ->action(DestroyAction::class),
        ]);

    $this->table->key('id')->setHeadings([
        KeyColumn::make('id'),

        Column::make('name')
            ->alias('title'),

        Column::make('status')
            ->transformer(fn ($state) => $state?->name)
            ->state(fn ($record) => $record->status)
            ->extra(fn ($state) => [
                'variant' => match ($state) {
                    Status::Available => 'success',
                    Status::Unavailable => 'danger',
                    Status::ComingSoon => 'info',
                    default => dd($state),
                },
            ]),
    ]);

    $this->table->setRecords(
        Product::query()->paginate(10)->items()
    );
});

it('transforms records', function () {
    $this->pipe->instance($this->table);

    $this->pipe->run();

    expect($this->table->getRecords())
        ->each(fn ($record) => $record
            ->toBeArray()
            ->toHaveKeys(['id', 'title', 'status', 'class', '_key', 'operations'])
        );
});
