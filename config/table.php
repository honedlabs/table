<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Search configuration
    |--------------------------------------------------------------------------
    |
    | This option controls the default search configuration for a table when 
    | not overriden on an inidiual table basis. The key is the query string
    | parameter to use for the search term. You can specify to use scout 
    | searching for every table, this assumes you have Scout configured.
    */
    'search' => [
        'key' => 'q',
        'use_scout' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Remember configuration
    |--------------------------------------------------------------------------
    */
    'remember' => [
        'enabled' => false,
        'duration' => 30*24*60*60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Sorting configuration
    |--------------------------------------------------------------------------
    */
    'sorting' => [
        'sort_key' => 'sort',
        'order_key' => 'order',
        'default_order' => 'asc',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination configuration
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'default_per_page' => 10,
        'name' => 'page',
        'options' => null,
        'key' => 'show'
    ],

    /*
    |--------------------------------------------------------------------------
    | Chunking configuration
    |--------------------------------------------------------------------------
    */
    'chunking' => [
        'by_id' => true,
        'size' => 500,
    ],
];