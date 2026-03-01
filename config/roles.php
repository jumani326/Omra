<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rôles du système UMS — 4 rôles uniquement
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'agence' => 'agence',
        'ministere' => 'ministere',
        'guide' => 'guide',
        'pelerin' => 'pelerin',
    ],

    'permissions' => [
        'agence' => [
            'manage_own_pilgrims',
            'manage_own_guides',
            'manage_own_accounting',
            'manage_own_visas',
            'view_own_data',
        ],
        'ministere' => [
            'audit_all',
            'manage_agencies',
            'validate_agencies',
            'validate_visas',
            'view_all_pilgrims',
            'view_statistics',
        ],
        'guide' => [
            'view_own_group',
            'checkin_checkout',
            'view_group_pilgrims',
        ],
        'pelerin' => [
            'view_own_data',
        ],
    ],
];
