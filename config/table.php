<?php

return [
    'endpoint' => '/actions',

    'paginator' => 'length-aware',

    'pagination' => [
        'options' => 10,
        'default' => 10,
    ],

    'toggle' => [
        'enabled' => false,
        'order' => false,
        'remember' => false,
        'duration' => 15768000,
    ],

    'matching' => false,

    'keys' => [
        'searches' => 'search',
        'matches' => 'matches',
        'sorts' => 'sort',
        'pages' => 'page',
        'columns' => 'columns',
        'records' => 'rows',
    ],
];
