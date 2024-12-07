<?php

use Workbench\App\Models\Product;

it('development', function () {
    dd(Product::getModel()::class);
});
