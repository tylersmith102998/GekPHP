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
$config['tables']['sessions_layout'] = [
    "id" => [
        "data_type" => "bigint(21)",
        "auto_inc" => true
    ],
    "token" => [
        "data_type" => "varchar(255)"
    ],
    "user_id" =>
    [
        "data_type" => "bigint"
    ],
    "ip_address" => [
        "data_type" => "varchar(15)"
    ],
    "creation_date" => [
        "data_type" => "bigint"
    ],
    "last_access_time" => [
        "data_type" => "bigint"
    ],
    "last_page_loaded" => [
        "data_type" => "varchar(255)",
        "default" => "/"
    ],
    "remembered" => [
        "data_type" => "enum('0','1')",
        "default" => "0"
    ],
    "valid" => [
        "data_type" => "enum('0','1')",
        "default" => "1"
    ],
    "_primary_keys" => ["id", "token"]
];

$config['database'] = [];
$config['database']['errors'] = [];
$config['database']['errors']['connection'] = "There was an issue connecting to the Database. Please try again later.";

$config['registration'] = [];
$config['registration']['errors'] = [];
$config['registration']['errors']['username_exists']    = "Username already exists.";
$config['registration']['errors']['email_exists']       = "An account is already registered with that e-mail address.";
$config['registration']['success']                      = "Your account has been registered successfully.";

if ($config['require_activation'])
    $config['registration']['success'] .= " Check your email for an activation link.";
else
    $config['registration']['success'] .= " You may now log in.";

$config['login'] = [];
$config['login']['errors'] = [];
$config['login']['errors']['invalid_credentials'] = "Invalid E-mail/Password combination.";

$config['sessions'] = [];
$config['sessions']['cookie_expiry'] = 86400;

$config['routes'] = [];
$config['routes']['login']      = "/home/login";
$config['routes']['register']   = "/home/register";
$config['routes']['logout']     = "/home";

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
