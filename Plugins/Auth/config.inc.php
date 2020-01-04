<?php

return [
    'tables' => [
        'users_layout' => [
            'id' => [ // DONT TOUCH
                'data_type' => 'bigint',
                'auto_inc' => true
            ],
            'username' => [ // DONT TOUCH
                'data_type' => 'varchar(15)'
            ],
            'password' => [ // DONT TOUCH
                'data_type' => 'varchar(64)'
            ],
            'salt' => [ // DONT TOUCH
                'data_type' => 'varchar(64)'
            ],
            'activated' => [ // DONT TOUCH
                'data_type' => 'enum(0,1)'
            ],
            'activation_token' => [ // DONT TOUCH
                'data_type' => 'varchar(64)'
            ],
            '_primary_keys' => ['id']
        ]
    ],

    ['require_activation'] => false,
];
