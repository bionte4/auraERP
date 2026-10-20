<?php

return [
    'title' => 'Role & Access Summary',

    'navigation' => [
        'label' => 'Role Summary',
    ],

    'intro' => [
        'heading'     => 'What can each role do?',
        'description' => 'Read-only overview across :total available permissions. Click a role card to focus the matrix on that role; click an item name to see the exact permission names. Editing happens in Roles.',
    ],

    'search' => [
        'placeholder' => 'Search permission or item, e.g. delete_employee…',
    ],

    'card' => [
        'permissions' => 'permissions',
        'users'       => 'user(s)',
        'resources'   => 'Resources',
        'pages'       => 'Pages',
        'widgets'     => 'Widgets',
    ],

    'categories' => [
        'resources' => 'Resources',
        'pages'     => 'Pages',
        'widgets'   => 'Widgets',
    ],

    'table' => [
        'item' => 'Item',
    ],

    'empty' => 'No items match your search.',
];
