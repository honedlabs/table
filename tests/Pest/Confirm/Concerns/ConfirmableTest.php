<?php

declare(strict_types=1);

use Honed\Core\Concerns\Evaluable;
use Honed\Table\Confirm\Concerns\Confirmable;
use Honed\Table\Confirm\Confirm;

class ConfirmableTest
{
    use Confirmable;
    use Evaluable;
}

beforeEach(function () {
    $this->test = new ConfirmableTest;
});

it('has no confirm by default', function () {
    expect($this->test)
        ->isConfirmable()->toBeFalse()
        ->getConfirm()->toBeNull();
});

it('sets confirm', function () {
    $this->test->setConfirm(Confirm::make('Title'));
    expect($this->test)
        ->getConfirm()->toBeInstanceOf(Confirm::class)
        ->getConfirm('title')->toBe('Title')
        ->isConfirmable()->toBeTrue();
});

it('rejects null values', function () {
    $this->test->setConfirm(Confirm::make('Title'));
    $this->test->setConfirm(null);
    expect($this->test)
        ->getConfirm()->toBeInstanceOf(Confirm::class)
        ->getConfirm('title')->toBe('Title')
        ->isConfirmable()->toBeTrue();
});

it('chains confirm', function () {
    expect($this->test->confirm(Confirm::make('Title')))->toBeInstanceOf(ConfirmableTest::class)
        ->getConfirm()->toBeInstanceOf(Confirm::class)
        ->getConfirm('title')->toBe('Title')
        ->isConfirmable()->toBeTrue();
});

it('chain rejects null values', function () {
    expect($this->test->confirm(Confirm::make('Title'))->confirm(null))->toBeInstanceOf(ConfirmableTest::class)
        ->getConfirm()->toBeInstanceOf(Confirm::class)
        ->getConfirm('title')->toBe('Title')
        ->isConfirmable()->toBeTrue();
});

it('chains confirm with string', function () {
    expect($this->test->confirm('Title'))->toBeInstanceOf(ConfirmableTest::class)
        ->isConfirmable()->toBeTrue()
        ->getConfirm()->toBeInstanceOf(Confirm::class)
        ->getConfirm('title')->toBe('Title');
});

it('chains confirm with assignments', function () {
    expect($this->test->confirm([
        'title' => 'The title goes heres',
        'description' => 'The description goes heres',
        'success' => 'Success',
    ]))->toBeInstanceOf(ConfirmableTest::class)
        ->isConfirmable()->toBeTrue()
        ->getConfirm('title')->toBe('The title goes heres')
        ->getConfirm('description')->toBe('The description goes heres')
        ->getConfirm('success')->toBe('Success');
});

it('chains confirm with closures', function () {
    expect($this->test->confirm(fn (Confirm $confirm) => $confirm
        ->title('The title goes heres')
        ->description('The description goes heres')
        ->success('Success'))
    )->toBeInstanceOf(ConfirmableTest::class)
        ->isConfirmable()->toBeTrue()
        ->getConfirm('title')->toBe('The title goes heres')
        ->getConfirm('description')->toBe('The description goes heres')
        ->getConfirm('success')->toBe('Success');
});
