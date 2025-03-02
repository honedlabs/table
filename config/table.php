<?php

return [
    'endpoint' => '/actions',

    'paginator' => 'length-aware',

    'delimiter' => ',',

    'pagination' => [
        /** The options for the table, int or array of ints */
        'options' => 10,
        /** This should be a value in the provided options */
        'default' => 10,
    ],

    'toggle' => [
        /** Whether all tables should be toggleable */
        'enabled' => false,
        /** Whether all tables should have cookies enabled */
        'remember' => false,
        /** How long the cookie should be stored for, this is 1 year */
        'duration' => 15768000,
    ],

    /** Whether the search columns can be toggled */
    'matches' => false,

    'config' => [
        /** The delimiter for the search parameter to extract an array */
        'delimiter' => ',',
        /** The parameter name for the search parameter */
        'searches' => 'search',
        /** The parameter name for the column match array */
        'matches' => 'match',
        /** The parameter name for the sort parameter */
        'sorts' => 'sort',
        /** The parameter name for the page number */
        'pages' => 'page',
        /** The parameter name for the columns to display */
        'columns' => 'cols',
        /** The parameter name for the number of records to display */
        'records' => 'rows',
    ],
];
