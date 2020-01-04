<?php

namespace Config;

use ErrorHandling\Exceptions\DataTypeException;
use ErrorHandling\Exceptions\FileNotFoundException;

class Config
{

    /**
     * Caches all the configs, rather than having to read from the harddisk constantly.
     * @var array
     */
    private static $configs = [];

    /**
    * Takes a config filename in the config folder, finds it, and loads (returns) it.
    * @param  string $path The filename
    * @return array        The config array located at the path.
    */
    public static function load($path)
    {
        $path = CONFIG . $path . '.php';

        // return config if it was already loaded.
        if (isset(static::$configs[$path]))
        {
            return static::$configs[$path];
        }

        // Checks for existing file, throws exception if not found.
        if (!file_exists($path))
        {
            throw new FileNotFoundException("{$path} does not exist", 404);
        }

        // Get the data
        $config_data = include($path);

        // Make sure it's array format, throw exception if not.
        if (!is_array($config_data))
        {
            throw new DataTypeException("Config file data at {$path} is not an array!", 1);
        }

        // cache the config
        static::$configs[$path] = $config_data;

        // Feed data back
        return $config_data;
    }

}
