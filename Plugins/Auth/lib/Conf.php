<?php

/**
 * Auth/Conf Library
 *
 * This library is mainly responsible for getting a config value through dot
 * notation (registration.errors.user_exists, for example)
 */

namespace Plugins\Auth\Libs;

class Conf
{

    /**
     * The config array
     * @var array
     */
    private $config = [];

    /**
     * We use init as constructors so we can maintain control of the method call.
     * @param  array $config  The config array
     * @return void
     */
    public function init($config)
    {
        $this->config = $config;
    }

    /**
     * Gets a config value from the array using dot notation.
     * @param  string $path Dot notation config location
     * @return mixed        Value of the config
     */
    public function get($path)
    {
        // Break the string up and copy config to local
        $path = explode('.', $path);
        $config = $this->config;

        // Run through each section and check if it's set, then set config to
        // the new array.
        foreach ($path as $c)
        {
            if (isset($config[$c]))
            {
                $config = $config[$c];
            }
        }

        // Return last successful value.
        return $config;
    }

}
