<?php

use Honed\Table\Table;

beforeEach(function () {
    Table::useEndpoint(Table::Endpoint);
    $this->table = exampleTable();
});

it('can get the default endpoint', function () {
    expect(Table::getDefaultEndpoint())->toBe(Table::Endpoint);
});

it('can get the endpoint', function () {
    expect($this->table->getEndpoint())->toBe(Table::Endpoint);
});

it('can set the endpoint', function () {
    $this->table->setEndpoint('/test');

    expect($this->table->getEndpoint())->toBe('/test');
});

it('rejects null endpoint', function () {
    $this->table->setEndpoint(null);

    expect($this->table->getEndpoint())->toBe(Table::Endpoint);
});

it('can configure the endpoint globally', function () {
    Table::useEndpoint('/test');

    expect(Table::getDefaultEndpoint())->toBe('/test');
    expect($this->table->getEndpoint())->toBe('/test');
});
