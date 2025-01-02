<?php

beforeEach(function () {
    $this->table = exampleTable();
});

it('can get the endpoint', function () {
    expect($this->table->getEndpoint())->toBe(config('table.endpoint'));
});

it('can set the endpoint', function () {
    $this->table->setEndpoint('/test');

    expect($this->table->getEndpoint())->toBe('/test');
});

it('rejects null endpoint', function () {
    $this->table->setEndpoint(null);

    expect($this->table->getEndpoint())->toBe(config('table.endpoint'));
});
