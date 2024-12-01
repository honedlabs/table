<?php

use Illuminate\Support\Facades\Http;
use Honed\Table\Tests\RequestFactories\FilterRequest;
use function Pest\Laravel\{get};

it('tests', function () {
    FilterRequest::new()->fake();
    $response = get('/');
    dd($response);
});