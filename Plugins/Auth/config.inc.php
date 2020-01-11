<?php

$config = [];

$config['require_activation'] = false;

$config['tables'] = [];
$config['tables']['users_layout'] = [
    "id" => [
        "data_type" => "bigint",
        "auto_inc" => true
    ],
    "email" => [
        "data_type" => "varchar(128)"
    ],
    "username" => [
        "data_type" => "varchar(15)"
    ],
    "password" => [
        "data_type" => "varchar(255)"
    ],
    "activated" => [
        "data_type" => "enum('0','1')",
        "default" => "0"
    ],
    "activation_token" => [
        "data_type" => "varchar(64)",
        "default" => "0"
    ],
    "permission_level" => [
        "data_type" => "enum('1','2','3')",
        "default" => "1"
    ],
    "_primary_keys" => ["id"]
];

$config['registration'] = [];
$config['registration']['errors'] = [];
$config['registration']['errors']['username_exists'] = "Username already exists.";
$config['registration']['errors']['email_exists'] = "An account is already registered with that e-mail address.";
$config['registration']['success'] = "Your account has been registered successfully.";

if ($config['require_activation'])
    $config['registration']['success'] .= " Check your email for an activation link.";
else
    $config['registration']['success'] .= " You may now log in.";

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
