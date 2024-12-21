<?php

use Honed\Table\Table;

beforeEach(function () {
    $this->table = exampleTable();
    Table::useExtraction(false);
});

it('is not extractable by default', function () {
    expect($this->table->isExtractable())->toBeFalse();
    expect($this->table->usesExtraction())->toBeFalse();
    expect($this->table->isNotExtractable())->toBeTrue();
});

it('can be set to', function () {
    $this->table->setExtract(true);

    expect($this->table->isExtractable())->toBeTrue();
});

it('can be globally configured', function () {
    Table::useExtraction(true);

    expect($this->table->isExtractable())->toBeTrue();
    expect($this->table->usesExtraction())->toBeTrue();
});
