<?php

namespace Networking;

/**
 * Handles GET, POST, and other server requests.
 */
class Request
{

    /**
     * Gets a get var, or returns false if not present.
     * @param  string $var Get var name
     * @return mixed       Value of the request var
     */
    public function get($var)
    {
        if (isset($_GET[$var]))
        {
            return $_GET[$var];
        }
        else
        {
            return false;
        }
    }

    /**
     * Gets a post var, or returns false if not present.
     * @param  string $var Post var name
     * @return mixed       Value of the request var
     */
    public function post($var)
    {
        if (isset($_POST[$var]))
        {
            return $_POST[$var];
        }
        else
        {
            return false;
        }
    }

}
