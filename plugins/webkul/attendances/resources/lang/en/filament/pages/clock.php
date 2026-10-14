<?php

return [
    'navigation' => 'Clock',
    'title'      => 'Clock',

    'actions' => [
        'clock-in'  => 'Clock in',
        'clock-out' => 'Clock out',
        'locating'  => 'Finding your location...',
        'enroll'    => 'Enroll face',
        'reenroll'  => 'Enroll face again',
    ],

    'face' => [
        'loading'      => 'Preparing the camera and face model...',
        'scanning'     => 'Looking for a face...',
        'ready'        => 'Face is enrolled. Face the camera, then clock.',
        'needs-enroll' => 'Face the camera, then enroll once.',
        'desktop'      => 'A laptop does not use the camera. Press clock, then allow location.',
    ],

    'status' => [
        'none'     => 'You have not clocked in today.',
        'open'     => 'Clocked in at :in (:status).',
        'complete' => 'Today is closed. In :in, out :out.',
    ],

    'missing-employee' => [
        'title' => 'Employee record not found',
        'body'  => 'This account is not linked to an employee, so clocking from this phone is unavailable.',
    ],

    'location-note' => 'Your phone will ask for location permission. Coordinates are saved only when you clock in or out. On a phone, open this site over HTTPS so the browser can share GPS.',

    'face-note' => 'The front camera is used only on a phone, when you press a button, to match your face. No photo is stored. Open this site over HTTPS. A photo of a face can fool this match, so use it at a supervised workplace.',

    'desktop-note' => 'On a laptop or netbook, clocking uses location only. The camera and face match are used on a phone.',

    'sites-note' => 'Clocking is accepted when your location is inside one of these work sites: :sites.',

    'sites-empty' => 'No work sites are configured yet. Any location is still accepted until an administrator adds sites under Work sites.',

    'tracking' => [
        'active' => 'Location trail is on. Keep this page open. Points saved:',
        'note'   => 'While attendance is open, the phone sends a location about every 30 seconds. Sending stops when you clock out, leave this page, or lock the screen.',
    ],

    'links' => [
        'check-in'  => 'Clock-in location: :place',
        'check-out' => 'Clock-out location: :place',
    ],

    'notifications' => [
        'clock-in' => [
            'title' => 'Clock in recorded',
            'body'  => 'Your arrival and location for today have been saved.',
        ],
        'clock-out' => [
            'title' => 'Clock out recorded',
            'body'  => 'Your departure and location for today have been saved.',
        ],
        'already-in' => [
            'title' => 'Already clocked in',
            'body'  => 'You already have a clock-in for today.',
        ],
        'missing-open' => [
            'title' => 'No open clock-in',
            'body'  => 'Clock in first, or today is already closed.',
        ],
        'missing-employee' => [
            'title' => 'Employee record not found',
            'body'  => 'Your account is not linked to an employee.',
        ],
        'location-denied' => [
            'title' => 'Location permission is required',
            'body'  => 'Allow location access in the browser, then try again.',
        ],
        'location-unavailable' => [
            'title' => 'This device cannot share a location',
            'body'  => 'Clock in from a phone browser that supports location.',
        ],
        'location-invalid' => [
            'title' => 'Location could not be read',
            'body'  => 'Try again from a place with a clearer GPS signal.',
        ],
        'outside-site' => [
            'title' => 'Outside every work site',
            'body'  => 'You are outside the radius of every active work site. Move closer to the project site, then try again.',
        ],
        'forbidden' => [
            'title' => 'You cannot clock this attendance',
            'body'  => 'Your account does not have permission for this action.',
        ],
        'face-enrolled' => [
            'title' => 'Face enrolled',
            'body'  => 'The next clock in and clock out will match this face.',
        ],
        'face-not-found' => [
            'title' => 'No face in view',
            'body'  => 'Face the camera in enough light, then try again.',
        ],
        'face-missing' => [
            'title' => 'Face is not enrolled',
            'body'  => 'Enroll your face once before clocking.',
        ],
        'face-mismatch' => [
            'title' => 'Face does not match',
            'body'  => 'The face in the camera does not match the data enrolled for this employee.',
        ],
        'face-invalid' => [
            'title' => 'Face data could not be read',
            'body'  => 'Reload the page, then enroll or clock again.',
        ],
        'camera-denied' => [
            'title' => 'Camera is required',
            'body'  => 'Allow the camera in the browser. On a phone, the site must be opened over HTTPS.',
        ],
    ],
];
