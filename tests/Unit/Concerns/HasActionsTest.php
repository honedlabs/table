<?php

use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Actions\PageAction;

beforeEach(function () {
    $this->table = exampleTable();
    $this->blank = blankTable();
});

it('can determine if the table has no actions', function () {
    expect($this->blank->missingActions())->toBeTrue();
    expect($this->blank->hasActions())->toBeFalse();

    expect($this->table->missingActions())->toBeFalse();
    expect($this->table->hasActions())->toBeTrue();
});

it('can set actions', function () {
    $this->blank->setActions([
        InlineAction::make('test'),
    ]);

    expect($this->blank->getActions())->toHaveCount(1);
});

it('rejects null actions', function () {
    $this->table->setActions(null);

    expect($this->table->getActions())->not->toBeEmpty();
});

it('can get actions', function () {
    expect($this->table->getActions())->toBeCollection()
        ->not->toBeEmpty();

    expect($this->blank->getActions())->toBeCollection()
        ->toBeEmpty();
});

it('can get inline actions', function () {
    expect($this->table->getInlineActions())->toBeCollection()
        ->not->toBeEmpty()
        ->every(fn ($action) => $action instanceof InlineAction)->toBeTrue();
});

it('can get bulk actions', function () {
    expect($this->table->getBulkActions())->toBeCollection()
        ->not->toBeEmpty()
        ->every(fn ($action) => $action instanceof BulkAction)->toBeTrue();
});

it('can get page actions', function () {
    expect($this->table->getPageActions())->toBeCollection()
        ->not->toBeEmpty()
        ->every(fn ($action) => $action instanceof PageAction)->toBeTrue();
});
