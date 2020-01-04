<?php

$config = [];

$config['tables'] = [];
$config['tables']['users_layout'] = [
    "id" => [
        "data_type" => "bigint",
        "auto_inc" => true
    ],
    "username" => [
        "data_type" => "varchar(15)"
    ],
    "password" => [
        "data_type" => "varchar(64)"
    ],
    "salt" => [
        "data_type" => "varchar(64)"
    ],
    "activated" => [
        "data_type" => "enum(0,1)"
    ],
    "activation_token" => [
        "data_type" => "varchar(64)"
    ],
    "_primary_keys" => ["id"]
];

return $config;

/*
print_r([
    "users_table_layout" => [
        "id" => ["data_type" => "bigint",
            "auto_inc" => true
        ],
        "username" => [
            "data_type" => "varchar(15)"
        ],
        "password" => [
            "data_type" => "varchar(64)"
        ],
        "salt" => [
            "data_type" => "varchar(64)"
        ],
        "activated" => [
            "data_type" => "enum(0,1)"
        ],
        "activation_token" => [
            "data_type" => "varchar(64)"
        ],
        "_primary_keys" => ["id"]
    ],

    ["require_activation"] => false,
]);
 */
