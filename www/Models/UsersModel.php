<?php

namespace Models;

class UsersModel extends \Core\BaseModel
{

    public function __construct()
    {
        //$this->name = get_class($this);

        $this->layout = [
            '_primary_keys' => ['id'],
            'id' => [
                'data_type' => 'BIGINT',
                'auto_inc' => true
            ],
            'email' => [
                'data_type' => 'VARCHAR(128)'
            ],
            'password' => [
                'data_type' => 'VARCHAR(64)'
            ]
        ];

        parent::__construct();
    }

}
