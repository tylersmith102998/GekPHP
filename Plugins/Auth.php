<?php

namespace Plugins;

class Auth extends \Core\BasePlugin
{

    private $Users = null;

    public function __construct()
    {
        parent::__construct();

        $this->load_config();

        $this->Users = $this->Model->load('users', $this->config['tables']['users_layout']);
    }

}
