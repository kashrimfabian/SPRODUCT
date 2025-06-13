<?php
return [
    'roles' => [
        'admin' => [
            'admin.dashboard',
            'admin.manageUsers',
            'admin.editUser',
            'admin.updateUser',
            'admin.deleteUser',
            'admin.viewProduction',
            'admin.viewSales',
        ],
        'superadmin' => [
            'superadmin.dashboard',
            'superadmin.resetPassword',
            'superadmin.createUser',
            'superadmin.editUser',
            'superadmin.updateUser',
            'superadmin.deleteUser',
        ],
    ],
];
