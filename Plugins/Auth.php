<?php

/**
 * GekPHP Auth Plugin
 *
 * This plugin allows easy prototyping of a user table to streamline the process
 * of creating a unified login/registration system. You can change and mess
 * with the config file to fine-tune this plugin for your needs. (You can even
 * alter the table layout, just make sure you keep the ones that are already
 * there.)
 */

namespace Plugins;

class Auth extends \Core\BasePlugin
{

    /**
     * Defined constants for this class.
     */
    const AUTH_GUEST = 0;
    const AUTH_USER = 1;
    const AUTH_MODERATOR = 2;
    const AUTH_ADMIN = 3;

    /**
     * Holds any errors given during execution.
     * @var array
     */
    private $errors = [];

    /**
     * Name of libraries to load in.
     * @var array
     */
    public $libraries = [
        'Conf' => []
    ];

    /**
     * Holds the Users table model.
     * @var Plugins\Auth\Models\UsersModel
     */
    private $Users = null;

    /**
     * Load config, call parent constructor, and init the libraries. Also loads
     * in the Users model.
     */
    public function __construct()
    {
        $this->load_config();
        parent::__construct();

        $this->Conf->init($this->config);

        //print_r($this->Conf->get('tables.users_layout'));
        $this->Users = $this->Model->load('users', $this->Conf->get('tables.users_layout'));
    }

    /**
     * Performs an attempt to register the user.
     *
     * This function will check to make sure nobody else in the database is
     * registered under the same username or email, and also will throw an
     * error if it encounters a problem with the query.
     *
     * This function WILL NOT CHECK to make sure the user typed the correct
     * password. Please check this on the front end for any application where
     * it is crucial the user typed the correct one they wanted. (Unless you
     * have a view password button.)
     *
     * @param  array    $data     The data the user entered into form fields.
     * @param  callable $callback This function will be called upon completion of execution. Data $d is passed in.
     * @return callable           Execution result of the callback function.
     */
    public function register($data, callable $callback)
    {
        if ($this->check_email_taken($data['email']))
        {
            $this->error($this->Conf->get('registration.errors.email_exists'));
        }

        if ($this->check_username_taken($data['username']))
        {
            //exit('ut');
            $this->error($this->Conf->get('registration.errors.username_exists'));
        }

        //exit('end');

        // Generate activation token and hash the password.
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // Checks to see if app should require email activation.
        if (!$this->Conf->get('require_activation'))
        {
            $data['activated'] = '1';
        }
        else
        {
            $data['activation_token'] = $this->generate_token();
        }

        // Check if any errors generated.
        if ($this->errors != [])
        {
            $ret = [
                'error' => true,
                'message' => $this->errors
            ];
        }
        else
        {
            $ret = [
                'error' => false,
                'message' => [$this->Conf->get('registration.success')]
            ];

            // If we are unable to insert user data due to query or connection error, write that error.
            if (!$this->Users->insert($data))
            {
                $ret['error'] = true;
                $ret['message'] = $this->Conf->get('registration.errors.connection');
                //$this->error($this->Conf->get('registration.errors.connection'));
            }
        }

        // Call the callback function and pass return values to it.
        return $callback($ret);
    }

    /**
     * Checks the database to see if the username provided is taken by a user.
     * @param  string $username the username to test
     * @return bool             if the username is taken or not
     */
    private function check_username_taken($username)
    {
        $q = $this->Users->select(['id'], ['username' => ['=', $username]]);
        //var_dump($q);
        return (empty($q)) ? false : true;
    }

    /**
     * Checks the database to see if the email provided is taken by a user.
     * @param  string $email the email address to test
     * @return bool          if the username is taken or not
     */
    private function check_email_taken($email)
    {
        $q = $this->Users->select(['id'], ['email' => ['=', $email]]);
        return (empty($q)) ? false : true;
    }

    /**
     * Writes to the error array.
     * @param  string $str Error message
     * @return void
     */
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

}
