<?php

namespace Plugins;

class Auth extends \Core\BasePlugin
{

    private $errors = [];

    public $libraries = [
        'Conf' => []
    ];

    private $registration_defaults = [
        'activated' => '0',
    ];

    private $Users = null;

    public function __construct()
    {
        $this->load_config();
        parent::__construct();

        $this->Conf->init($this->config);

        //print_r($this->Conf->get('tables.users_layout'));
        $this->Users = $this->Model->load('users', $this->Conf->get('tables.users_layout'));
    }

    public function register($data, callable $callback)
    {
        $data = array_merge($this->registration_defaults, $data);

        if ($this->Users->select([], ['username' => '=' . $data['username']]))
        {
            $this->error($this->Conf->get('registration.errors.username_exists'));
        }

        if ($this->Users->select([], ['email' => '=' . $data['email']]))
        {
            $this->error($this->Conf->get('registration.errors.email_exists'));
        }

        $data['activation_token'] = $this->generate_token();
        $data['salt'] = $this->generate_salt();

        return $callback($this);
    }

    private function error($str)
    {
        $this->errors[] = $str;
    }

    /**
     * Generates a token for use by sessions.
     * @param  integer $len Chat length of the token
     * @return string       the token
     */
    private function generate_token($len = 64)
    {
        $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = str_repeat($str, 10);
        $str = str_shuffle($str);
        $rand = \random_int(0, strlen($str)-$len);

        return substr($str, $rand, $len);
    }

    /**
     * Generates a salt for use in hashing
     * @param  integer $len Length of the salt
     * @return string       The randomly generated salt
     */
    private function generate_salt($len = 32)
    {
        $str = "!@#$%^&*()-_=+[{\\|;:'\",<.>/?\"'}]";
        $str = str_repeat($str, 10);
        $str = str_shuffle($str);
        $rand = \random_int(0, strlen($str)-$len);

        return substr($str, $rand, $len);
    }

}
