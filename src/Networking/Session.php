<?php

/**
 * Session.php
 *
 * This class is responsible for PHP session handling. It will auto-check if
 * sessions have been started with each use.
 */

namespace Networking;

class Session
{

    /**
     * If PHP sessions have been started.
     * @var bool
     */
    private static $started = false;

    /**
     * Sets a session value by name.
     * @param string $name variable name
     * @param mixed  $val  value
     */
    public static function set($name, $val)
    {
        static::check_start();

        $_SESSION[$name] = $val;
    }

    /**
     * Gets a set session, returns false if none found.
     * @param  string $name variable name
     * @return mixed|false  result if found, false if not
     */
    public static function get($name)
    {
        static::check_start();

        if (isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }

        return false;
    }

    /**
     * Checks to see if PHP sessions have been started and starts them if they
     * haven't.
     * @return void
     */
    private static function check_start()
    {
        if (!static::$started)
        {
            session_start();
            static::$started = true;
        }
    }

}
