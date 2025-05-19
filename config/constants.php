<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Business Unit Types
    |--------------------------------------------------------------------------
    |
    | These constants define the different business unit types in the system.
    | They are used to categorize and filter products and other data.
    |
    */
    'business_units' => [
        'bakery' => 'bakery',
        'cake_tools' => 'cake_tools',
        'academy' => 'academy',
    ],

    /*
    |--------------------------------------------------------------------------
    | Product Types
    |--------------------------------------------------------------------------
    |
    | These constants define the different product types in the system.
    |
    */
    'product_types' => [
        'bakery' => 'bakery',
        'cake_tool' => 'cake_tool',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Statuses
    |--------------------------------------------------------------------------
    |
    | Default status values for various models
    |
    */
    'statuses' => [
        'active' => 'active',
        'inactive' => 'inactive',
        'pending' => 'pending',
        'completed' => 'completed',
    ],
]; 