<?php

declare(strict_types=1);

use Honed\Action\Testing\InlineRequest;
use Workbench\App\Models\Product;
use Workbench\App\Tables\ProductTable;

use function Pest\Laravel\post;

beforeEach(function () {
    $this->product = Product::factory()->create();

    $this->table = ProductTable::make();

    $this->request = InlineRequest::fake()
        ->for($this->table)
        ->record($this->product->id)
        ->name('edit')
        ->fill();
});

it('executes the action', function () {
    $data = $this->request->getData();

    $response = post(route('actions.invoke', $this->table), $data);

    $response->assertRedirect();

    $this->assertDatabaseHas('products', [
        'id' => $this->product->id,
        'name' => 'Inline',
    ]);
});

it('does not execute non-existent action', function () {
    $data = $this->request
        ->record($this->product->id)
        ->name('create')
        ->getData();

    $response = post(route('table.invoke', $this->table->getRouteKey()), $data);

    $response->assertNotFound();
});
