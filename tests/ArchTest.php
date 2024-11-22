<?php

use Workbench\App\Tables\ProductTable;

use function Pest\Laravel\get;

// arch('does not use debugging functions')
//     ->expect(['dd', 'dump', 'ray'])
//     ->each->not->toBeUsed();

// arch()->preset()->php();
// arch()->preset()->security();
// arch()->preset()->relaxed();
// arch()->preset()->honed();

it('tests', function() {
    // dd(get(route('product.index')));
    dd(ProductTable::make()->toArray());
});