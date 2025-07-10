<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->name = 'name';
    $this->label = ucfirst($this->name);
    $this->column = Column::make($this->name);
});

it('makes', function () {
    expect($this->column)
        ->getName()->toBe($this->name)
        ->getLabel()->toBe($this->label)
        ->isActive()->toBeTrue();
});

it('has parameter', function () {
    expect($this->column)
        ->getParameter()->toBe($this->name)
        ->name('relation.column')->toBe($this->column)
        ->getParameter()->toBe('relation_column')
        ->alias('alias')->toBe($this->column)
        ->getParameter()->toBe('alias');
});

it('has count relationship', function () {
    $column = Column::make('users_count');

    expect($column)
        ->queryCallback()->toBeNull()
        ->count()->toBe($column)
        ->queryCallback()->toBeInstanceOf(Closure::class);
});

it('has exists relationship', function () {
    $column = Column::make('users_exists');

    expect($column)
        ->queryCallback()->toBeNull()
        ->exists()->toBe($column)
        ->queryCallback()->toBeInstanceOf(Closure::class);
});

it('has avg relationship', function () {
    $column = Column::make('users_avg_age');

    expect($column)
        ->queryCallback()->toBeNull()
        ->avg()->toBe($column)
        ->queryCallback()->toBeInstanceOf(Closure::class);

    expect($column)
        ->average()->toBe($column)
        ->queryCallback()->toBeInstanceOf(Closure::class);
});

it('has sum relationship', function () {
    $column = Column::make('users_sum_age');

    expect($column)
        ->queryCallback()->toBeNull()
        ->sum()->toBe($column)
        ->queryCallback()->toBeInstanceOf(Closure::class);
});

it('has max relationship', function () {
    $column = Column::make('users_max_age');

    expect($column)
        ->queryCallback()->toBeNull()
        ->max()->toBe($column)
        ->queryCallback()->toBeInstanceOf(Closure::class);
});

it('has min relationship', function () {
    $column = Column::make('users_min_age');

    expect($column)
        ->queryCallback()->toBeNull()
        ->min()->toBe($column)
        ->queryCallback()->toBeInstanceOf(Closure::class);
});

it('requires a column when using aggregate relationships', function () {
    Column::make('users_avg_age')->avg('users');
})->throws(InvalidArgumentException::class);

it('creates a value', function () {
    expect($this->column->value(null))
        ->toEqual([null, false]);

    $user = User::factory()->create();

    expect($this->column->value($user))
        ->toEqual([$user->name, false]);
});

it('has array representation', function () {
    expect($this->column->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => $this->name,
            'label' => $this->label,
            'hidden' => false,
            'active' => true,
            'toggleable' => true,
            'align' => Column::ALIGN_LEFT,
        ]);
});

it('has array representation with sort', function () {
    expect($this->column->sortable()->toArray())
        ->toBeArray()
        ->toEqual([
            'name' => $this->name,
            'label' => $this->label,
            'hidden' => false,
            'toggleable' => true,
            'active' => true,
            'align' => Column::ALIGN_LEFT,
            'sort' => [
                'active' => false,
                'direction' => null,
                'next' => $this->name,
            ],
        ]);
});

it('serializes to json', function () {
    expect($this->column->jsonSerialize())
        ->toBeArray()
        ->toEqual($this->column->toArray());
});

describe('evaluation', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();

        $this->column->record($this->user);
    });

    it('has named dependencies', function ($closure, $class) {
        expect($this->column->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        // fn () => [fn ($state) => $state, $this->user->name],
        fn () => [fn ($row) => $row, User::class],
        fn () => [fn ($record) => $record, User::class],
        fn () => [fn ($model) => $model, User::class],
    ]);

    it('has typed dependencies', function ($closure, $class) {
        expect($this->column->evaluate($closure))->toBeInstanceOf($class);
    })->with([
        fn () => [fn (Model $arg) => $arg, User::class],
        fn () => [fn (User $arg) => $arg, User::class],
    ]);
});
