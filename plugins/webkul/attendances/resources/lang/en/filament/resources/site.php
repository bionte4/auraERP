<?php

return [
    'navigation' => [
        'title' => 'Work sites',
    ],

    'model'  => 'Work site',
    'plural' => 'Work sites',

    'actions' => [
        'create' => 'New site',
    ],

    'form' => [
        'name'        => 'Site name',
        'latitude'    => 'Latitude',
        'longitude'   => 'Longitude',
        'radius'      => 'Radius',
        'radius-help' => 'Distance in meters from this point. Example: 200 meters for one project site. Copy the latitude and longitude from a map.',
        'active'      => 'Used for clocking',
    ],

    'table' => [
        'name'        => 'Name',
        'radius'      => 'Radius',
        'coordinates' => 'Point',
        'active'      => 'Active',
    ],
];
