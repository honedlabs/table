<?php

declare(strict_types=1);

use Honed\Table\Concerns\HasEndpoint;

class HasEndpointTest
{
    use HasEndpoint;
}

class HasEndpointMethodTest extends HasEndpointTest
{
    public function endpoint(): string
    {
        return '/method';
    }
}

class HasEndpointPropertyTest extends HasEndpointTest
{
    public string $endpoint = '/property';
}

beforeEach(function () {
    $this->test = new HasEndpointTest();
});

it('has default config endpoint', function () {
    expect($this->test->getEndpoint())
        ->toBe(config('table.endpoint'));
});

it('has property endpoint', function () {
    expect((new HasEndpointPropertyTest())->getEndpoint())->toBe('/property');
});

it('has method endpoint', function () {
    expect((new HasEndpointMethodTest())->getEndpoint())->toBe('/method');
});

it('sets endpoint', function () {
    $this->test->setEndpoint('/test');
    expect($this->test->getEndpoint())->toBe('/test');
});

it('rejects null values', function () {
    $this->test->setEndpoint('/test');
    $this->test->setEndpoint(null);
    expect($this->test)
        ->getEndpoint()->toBe('/test');
});
