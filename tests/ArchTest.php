<?php

use Workbench\App\Tables\ProductTable;

arch('does not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

// arch()->preset()->php();
// arch()->preset()->security();
// arch()->preset()->relaxed();
// arch()->preset()->honed();

it('tests', function() {
    dd(ProductTable::make()->toArray());
});