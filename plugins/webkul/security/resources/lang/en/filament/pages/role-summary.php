<?php

return [
    'title' => 'Role & Access Summary',

    'navigation' => [
        'label' => 'Role Summary',
    ],

    'intro' => [
        'heading'     => 'What can each role do?',
        'description' => 'Read-only overview across :total available permissions. Click role cards to compare them side by side in the matrix; click an item name to see the exact permission names. Editing happens in Roles.',
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
        'other'       => 'Other',
        'other-hint'  => 'Permissions not mapped to any resource, page, or widget (e.g. API or legacy permissions).',
        'baseline'    => 'Baseline',
        'custom'      => 'Custom',
        'show-users'  => 'Show users',
        'edit'        => 'Edit in Roles',
    ],

    'compare' => [
        'selected'         => ':count role(s) selected for comparison',
        'clear'            => 'Clear selection',
        'hint'             => 'Tip: click two or more role cards to compare them.',
        'only-differences' => 'Only show differences',
    ],

    'categories' => [
        'resources' => 'Resources',
        'pages'     => 'Pages',
        'widgets'   => 'Widgets',
    ],

    'table' => [
        'item' => 'Item',
    ],

    'empty' => 'No items match your search or filters.',
];
