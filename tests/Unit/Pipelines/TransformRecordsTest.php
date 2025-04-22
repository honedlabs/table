<?php

declare(strict_types=1);

use Honed\Action\InlineAction;
use Honed\Table\Columns\Column;
use Honed\Table\Columns\KeyColumn;
use Honed\Table\Pipelines\TransformRecords;
use Honed\Table\Table;
use Honed\Table\Tests\Stubs\Product;
use Honed\Table\Tests\Stubs\Status;
use Illuminate\Support\Arr;

beforeEach(function () {
    foreach (range(1, 10) as $i) {
        product();
    }

    $this->pipe = new TransformRecords();
    $this->next = fn ($table) => $table;

    $this->columns = [
        KeyColumn::make('id'),

        Column::make('name')
            ->always(),

        Column::make('price'),

        Column::make('description')
            ->sometimes(),

        Column::make('status')
            ->extra(fn ($record) => [
                'variant' => match ($record->status) {
                    Status::Available => 'success',
                    Status::Unavailable => 'danger',
                    Status::ComingSoon => 'info',
                },
            ])
            ->value(fn ($record) => $record->status?->name),

        Column::make('fixed')
            ->value('Constant')
    ];

    $this->actions = [
        InlineAction::make('view'),

        InlineAction::make('edit')
            ->allow(fn ($record) => $record->id % 2 === 0),

        InlineAction::make('delete'),
    ];

    $this->table = Table::make()
        ->actions($this->actions);

    $this->table->cacheColumns($this->columns);

    $this->table->setRecords(Product::query()->orderBy('id')->get()->all());
});

it('transforms records', function () {
    $this->pipe->__invoke($this->table, $this->next);

    expect($this->table->getRecords())
        ->each->toHaveCount(\count($this->columns) + 1);

    $first = Arr::first($this->table->getRecords());

    expect($first)
        ->{'actions'}->toHaveCount(2);

    $last = Arr::last($this->table->getRecords());

    expect($last)
        ->{'actions'}->toHaveCount(3);
});

it('serializes', function () {
    $this->table->serializes(true);

    $this->pipe->__invoke($this->table, $this->next);
    
    expect($this->table->getRecords())
        ->each->toHaveCount(11 + 1);    
});