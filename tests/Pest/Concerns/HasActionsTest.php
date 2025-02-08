<?php

declare(strict_types=1);

use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\Contracts\Action;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Actions\PageAction;
use Honed\Table\Concerns\HasActions;

class HasActionsTest
{
    use HasActions;

    protected $actions;
}

class HasActionsMethodTest extends HasActionsTest
{
    public function actions(): array
    {
        return [
            InlineAction::make('test'),
            PageAction::make('test'),
            BulkAction::make('test'),
        ];
    }
}

beforeEach(function () {
    $this->test = new HasActionsTest;
    $this->method = new HasActionsMethodTest;
});

it('is empty by default', function () {
    expect($this->test)
        ->hasActions()->toBeFalse();

    expect($this->method)
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(3);
});

it('sets actions', function () {
    $this->test->setActions([InlineAction::make('test')]);

    expect($this->test)
        ->hasActions()->toBeTrue()
        ->getActions()->scoped(fn ($actions) => $actions
        ->toBeCollection()
        ->toHaveCount(1)
        ->first()->scoped(fn ($action) => $action
        ->toBeInstanceOf(Action::class)
        ->getName()->toBe('test')
        )
        );
});

it('rejects null actions', function () {
    $this->test->setActions([InlineAction::make('test')]);
    $this->test->setActions(null);

    expect($this->test)
        ->hasActions()->toBeTrue()
        ->getActions()->toHaveCount(1);
});

it('gets actions from method', function () {
    expect($this->method)
        ->hasActions()->toBeTrue()
        ->getActions()->scoped(fn ($actions) => $actions
        ->toBeCollection()
        ->toHaveCount(3)
        );
});

it('gets inline actions', function () {
    expect($this->method)
        ->getInlineActions()->toBeCollection()
        ->each->toBeInstanceOf(InlineAction::class);
});

it('gets bulk actions', function () {
    expect($this->method)
        ->getBulkActions()->toBeCollection()
        ->each->toBeInstanceOf(BulkAction::class);
});

it('gets page actions', function () {
    expect($this->method)
        ->getPageActions()->toBeCollection()
        ->each->toBeInstanceOf(PageAction::class);
});
