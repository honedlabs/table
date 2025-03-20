<?php

declare(strict_types=1);

use Honed\Table\Concerns\HasClass;

beforeEach(function () {
    $this->test = new class {
        use HasClass;
    };
});

it('is empty', function () {
    expect($this->test)
        ->hasClass()->toBeFalse()
        ->getClass()->toBeNull();
});

it('sets', function () {
    expect($this->test)
        ->class('bg-red-500')->toBe($this->test)
        ->hasClass()->toBeTrue()
        ->getClass()->toBe('bg-red-500');
});

it('merges', function () {
    expect($this->test)
        ->class('bg-red-500')->toBe($this->test)
        ->class('text-white')->toBe($this->test)
        ->hasClass()->toBeTrue()
        ->getClass()->toBe('bg-red-500 text-white');
});
