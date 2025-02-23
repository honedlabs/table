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

    'matches' => false,

    'keys' => [
        'searches' => 'search',
        'matches' => 'match',
        'sorts' => 'sort',
        'pages' => 'page',
        'columns' => 'columns',
        'records' => 'rows',
    ],
];
