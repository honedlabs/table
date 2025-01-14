<?php

declare(strict_types=1);

use Honed\Table\Actions\InlineAction;
use Honed\Table\Columns\Column;
use Honed\Table\Concerns\HasRecords;
use Honed\Table\Tests\Stubs\Product;

class HasRecordsTest
{
    use HasRecords;
}

beforeEach(function () {
    HasRecordsTest::reduceRecords(false);
    $this->test = new HasRecordsTest();
});

it('has no records by default', function () {
    expect($this->test)
        ->hasRecords()->toBeFalse()
        ->getRecords()->toBeNull();
});

it('can set records', function () {
    $this->test->setRecords(collect([1, 2, 3]));
    expect($this->test)
        ->hasRecords()->toBeTrue()
        ->getRecords()->toBeCollection([1, 2, 3]);
});

it('can configure whether to reduce records', function () {
    HasRecordsTest::reduceRecords(true);

    expect($this->test)
        ->isReducing()->toBeTrue();
});

it('is not reducing by default', function () {
    expect($this->test)
        ->isReducing()->toBeFalse();
});

describe('formats', function () {
    beforeEach(function () {
        $this->columns = collect([
            Column::make('public_id'),
            Column::make('name'),
            Column::make('description'),
            Column::make('status')
        ]);

        $this->actions = collect([
            // InlineAction::make('show')->link(fn (Product $p) => route('product.show', $p->id)),
            InlineAction::make('show.other')->link->link(fn (mixed $record) => route('product.show', $record->id)),
            InlineAction::make('delete')->authorize(fn (Product $p) => $p->id !== 1)
        ]);

        foreach (\range(1, 10) as $_) {
            product();
        }
    });
    
    test('not with empty records', function () {
        expect($this->test->formatRecords(collect(), $this->columns))
            ->toBeCollection()
            ->toBeEmpty();
    });

    test('with columns', function () {
        expect($this->test->formatRecords(Product::all(), $this->columns))
            ->toBeCollection()
            ->toHaveCount(10)
            ->each(fn ($record) => $record
                ->toHaveKeys(['id', 'public_id', 'name', 'description', 'status', 'price', 'best_seller', 'created_at', 'updated_at'])
                ->not->toHaveKey('actions')
            );
    });

    test('with actions', function () {
        expect($this->test->formatRecords(Product::all(), $this->columns, $this->actions))
            ->toBeCollection()
            ->toHaveCount(10)
            ->dd()
            ->each(fn ($record) => $record->toHaveKey('actions'));
    });
});