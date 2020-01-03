<?php

/**
 * Cookie.php
 *
 * This class is responsible for handling PHP cookies and redefines the
 * set_cookie() function with Cookie->set()
 *
 * @see \Networking\Cookie->set
 */

namespace Networking;

class Cookie
{

    /**
     * Sets a new cookie.
     * @param string  $name name of the cookie
     * @param mixed   $val  value to assign
     * @param integer $exp  time in seconds cookie should last for
     * @param string  $path optional path that cookie is utilized in
     */
    public function set(string $name, $val, int $exp = 86400, $path = '/')
    {
        return setcookie($name, $val, time() + $exp, $path);
    }

    /**
     * Get the value of a cookie, or return false if it's not there.
     * @param  string $name name of the cookie
     * @return mixed|false   value of cookie if found, false if not.
     */
    public function get(string $name)
    {
        if (isset($_COOKIE[$name]))
        {
            return $_COOKIE[$name];
        }

        return false;
    }

}
