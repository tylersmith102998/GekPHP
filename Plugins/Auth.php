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

use \Networking\Session;
use \Networking\Cookie;

use \Plugins\Auth\Src\Account;

/**
 * Defined constants for this class.
 */
define('AUTH_GUEST', 0);
define('AUTH_USER', 1);
define('AUTH_MODERATOR', 2);
define('AUTH_ADMIN', 3);

class Auth extends \Core\BasePlugin
{

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
     * Name of sources and data structures to load in
     * @var array
     */
    public $sources = [
        'Account'
    ];

    /**
     * Account object.
     * @var \Plugins\Auth\Src\Account
     */
    public $Account = null;

    /**
     * Holds the Users table model.
     * @var Plugins\Auth\Models\UsersModel
     */
    private $Users = null;

    /**
     * Holds the Sessions table model.
     * @var Plugins\Auth\Models\SessionsModel
     */
    private $Sessions = null;

    /**
     * Contains an instance of the framework's Session handling class.
     * @var \Networking\Session
     */
    private $Session = null;

    /**
     * Load config, call parent constructor, and init the libraries. Also loads
     * in the Users model.
     */
    public function __construct()
    {
        $this->load_config();
        parent::__construct();

        // Initialize the library.
        $this->Conf->init($this->config);

        // Set instances of PHP Session and Cookie handlers.
        $this->Session = new Session();
        $this->Cookie = new Cookie();

        //print_r($this->Conf->get('tables.users_layout'));
        $this->Users = $this->Model->load('users', $this->Conf->get('tables.users_layout'));
        $this->Sessions = $this->Model->load('sessions', $this->Conf->get('tables.sessions_layout'));

        // Finalized. Find an existing session
        $this->login_from_session();
    }

    /**
     * Destroys the user's current login sesion.
     * @return void
     */
    public function logout()
    {
        $Session = $this->get_local_session();

        if ($Session->valid)
        {
            $this->kill_session($Session->token);
            $this->redirect($this->Conf->get('routes.logout'));
        }
    }

    /**
     * Restricts a page's access to a certain permission group, and redirects
     * the user if they do not fall within the category provided. Makes use of
     * the constants defined before class declaration.
     *
     * @param  int     $min_perm_level  Minimum permission level.
     * @param  int     $max_perm_level  Maximum permission level.
     * @param  string  $redirect        Where the application should redirect the user if this test fails.
     * @return void
     */
    public function restrict($min_perm_level, $max_perm_level = AUTH_ADMIN, $redirect = null)
    {
        // If no redirect passed, redirect to the login page.
        if ($redirect == null)
        {
            $redirect = $this->Conf->get('routes.login');
        }

        // Establish default of AUTH_GUEST, but check account for otherwise.
        $perm_level = AUTH_GUEST;
        if (isset($this->Account) && !empty($this->Account))
        {
            $perm_level = $this->Account->permission_level;
        }

        //var_dump($perm_level);

        if (($perm_level >= $min_perm_level) && ($perm_level <= $max_perm_level))
        {
            // Do nothing
        }
        else
        {
            // TODO: Redirect and set session for error message to that redirect page.
            $this->redirect($redirect);
        }
    }

    /**
     * Attempts to authenticate a user based off a session token.
     * @return void
     */
    public function login_from_session()
    {
        $Session = $this->get_local_session();

        if ($Session->valid)
        {
            $token = $Session->token;

            $Sq = $this->Sessions->get([], ['token' => ['=', $token]]);

            if (!empty($Sq))
            {
                //exit(print_r($Sq));
                $Session = (object) $Sq[0];

                $Uq = $this->Users->get([], ['id' => ['=', $Session->user_id]]);

                $this->logged_in = true;
                $this->Account = new Account($Uq[0]);

                $update = [
                    "last_access_time" => time(),
                    "last_page_loaded" => URI
                ];

                $w = [
                    'token' => ['=', $Session->token]
                ];

                $this->Sessions->update($update, $w);
                $this->refresh_cookie($Session->token);
            }
        }
    }

    /**
     * Attempts to log a user in, and if a correct combination of credentials
     * are provided, it will establish the user's login session.
     *
     * @param  array    $data      login credentials and remember me functionality
     * @param  callable $callback  The callback function which we will pass error information back to.
     * @return mixed               The result of the callback function.
     */
    public function login($data, callable $callback)
    {
        // Query table for results and establish defaults.
        $Eq = $this->Users->select([], ['email' => ['=', $data['email']]]);
        $ret = $this->cb_obj();
        $remember = false;

        // Check if remember me box result is passed.
        if (isset($data['_remember']))
        {
            $remember = $data['_remember'];
            unset($data['_remember']);
        }

        // Checks if the account couldn't be found by email.
        if (empty($Eq))
        {
            $this->error($this->Conf->get('login.errors.invalid_credentials'));
            $ret->error = true;
            $ret->message = $this->errors;
        }
        else
        {
            // If account was found:
            $account = $Eq[0];

            // Verify the password matches.
            if (!password_verify($data['password'], $account['password']))
            {
                $this->error($this->Conf->get('login.errors.invalid_credentials'));
                $ret->error = true;
                $ret->message = $this->errors;
            }
            else
            {
                // Successful login.
                $account = new Account($account);
                $this->generate_session($account, $remember, $ret);
            }
        }

        return $callback($ret);
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
     * @return mixed              Execution result of the callback function.
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
                $ret['message'] = $this->Conf->get('database.errors.connection');
                //$this->error($this->Conf->get('registration.errors.connection'));
            }
        }

        // Call the callback function and pass return values to it.
        return $callback($ret);
    }

    /**
     * Refreshes the cookie with new expiry time.
     * @param  string $token session token
     * @return void
     */
    private function refresh_cookie($token)
    {
        $this->Cookie->set($this->plugin_name, $token, $this->Conf->get('sessions.cookie_expiry'));
    }

    /**
     * Redirects the application user to the uri provided.
     * @param  string $uri URI to redirect to.
     * @return void
     */
    public function redirect($uri)
    {
        header("Location: {$uri}");
    }

    /**
     * Generates a session based off the account info and remember me box value.
     *
     * Errors can happen here, which is why we have passed a return object by
     * reference, so we can update the same instance of the object without
     * having to return it back to the calling method.
     *
     * @param  stdClass $Account  the account information
     * @param  bool     $remember whether or not to store your token in a cookie
     * @param  stdClass $ret      return object for error reporting
     * @return void
     */
    private function generate_session($Account, $remember, &$ret)
    {
        $time = time();
        $remember_s = ($remember) ? '1' : '0';

        $b_info = get_browser($_SERVER['HTTP_USER_AGENT']);
        $browser = $b_info->browser;
        $version = $b_info->version;
        $platform = $b_info->platform;

        $session = [
            'token' => $this->generate_token(),
            'user_id' => $Account->id,
            'ip_address' => REMOTE_IP,
            'browser_info' => "{$browser}, version {$version} (Running on {$platform})",
            'creation_date' => $time,
            'last_access_time' => $time,
            'last_page_loaded' => URI,
            'remembered' => $remember_s
        ];

        $Sq = $this->Sessions->insert($session);

        if (!$Sq)
        {
            $this->error($this->Conf->get('database.errors.connection'));
            $ret->error = true;
            $ret->message = $this->errors;
        }
        else
        {
            $this->kill_expired_sessions();
            $this->kill_expired_local_sessions();
            $this->establish_session((object) $session, $remember);
        }
    }

    /**
     * Kills expired sessions based on the cookie expiry config value.
     *
     * This method gets called every time Auth generates a session.
     *
     * TODO: Add config value to disable this feature. You might want to disable
     *       this in big datasets, and just use cronjobs to occasionally clean
     *       up the sessions table.
     *
     * @return void
     */
    private function kill_expired_sessions()
    {
        $w = [
            'last_access_time' => ['<', time() - $this->Conf->get('sessions.cookie_expiry')]
        ];

        $Sq = $this->Sessions->delete($w);
    }

    /**
     * Kills any local sessions that are expired.
     * @return void
     */
    private function kill_expired_local_sessions()
    {
        $Session = $this->get_local_session();

        if ($Session->valid)
        {
            $token = $Session->token;

            $w = [
                '_logic' => 'AND',
                'token' => ['=', $token],
                'last_access_time' => ['<', time() - $this->Conf->get('sessions.cookie_expiry')]
            ];

            $Sq = $this->Sessions->get([], $w);
            if (!empty($Sq))
            {
                $Sq = $this->Sessions->delete($w);
                $this->Session->destroy($this->plugin_name);
            }
        }
    }

    /**
     * Gets a user's local session.
     * @return stdClass Session object.
     */
    private function get_local_session()
    {
        // Set default.
        $Session = (object) [
            'valid' => false
        ];

        // Check for session, then cookie, finally init token to 0 if not set.
        if ($php_sesh = $this->Session->get($this->plugin_name))
        {
            $token = $php_sesh;
        }
        else if ($php_cookie = $this->Cookie->get($this->plugin_name))
        {
            $token = $php_cookie;
        }
        else
        {
            $token = '0';
        }

        // Fetch from DB
        $Sq = $this->Sessions->get([], ['token' => ['=', $token]]);

        // If session IS valid.
        if (!empty($Sq))
        {
            $Session = (object) $Sq[0];
        }

        return $Session;
    }

    /**
     * Establishes the PHP session, and if $remember == true, the cookie.
     * @param  \Networking\Session  $Session  Gek Session handler.
     * @param  boolean              $remember Whether to use cookies or not.
     * @return void
     */
    private function establish_session($Session, $remember = false)
    {
        $this->Session->set($this->plugin_name, $Session->token);

        if ($remember)
        {
            $this->Cookie->set($this->plugin_name, $Session->token,
                $this->Conf->get("sessions.cookie_expiry"));
        }
    }

    /**
     * Returns a template object for return objects to the callback functions.
     * @return stdClass Callback Object template.
     */
    private function cb_obj()
    {
        return (object) [
            'error' => false,
            'message' => [],
            'redirect' => function($uri) {
                header("Location: {$uri}");
            }
        ];
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
