<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Empty state
    |--------------------------------------------------------------------------
    |
    | You can enable or disable the matches feature, which allows your users to
    | select which columns they want to use to execute a search on the query.
    |
    | Enabling this will also provide a 'searches' property when serialized to
    | allow you to bind the options to a form input.
    |
    */

    'exporters' => [
        'eloquent' => Honed\Table\Exporters\EloquentExporter::class,
        'array' => Honed\Table\Exporters\ArrayExporter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | You can store the state of a filter view within a specific driver.
    |
    */
    'views' => [
        'uri' => '_views/{table}',

        'connection' => null,

        'table' => 'views',

        'driver' => 'database',
    ],
];
