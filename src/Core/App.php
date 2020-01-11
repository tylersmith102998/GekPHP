<?php

namespace Core;

use Config\Config;
use Core\DB;
use Core\Router;
use ErrorHandling\Exceptions\FileNotFoundException;
use ErrorHandling\Exceptions\MysqliException;
use Util\ClassRegistry;

/**
 * Core class for application functionality.
 */
class App
{

    /**
    * Default options for the config.
    * @var array
    */
    private $app_config_defaults = [
        'name' => 'Framework Application',
        'version' => '0.0.0',
        'author' => 'Anonymous',
        'route' => URI
    ];

    /**
     * Class registry object.
     * @var \Util\ClassRegistry
     */
    private $CR = null;

    /**
     * DB object that interfaces with the database.
     * @var Core\DB
     */
    private $DB = null;

    /**
     * Core Router object
     * @var Core\Router
     */
    private $Router = null;

    /**
    * Entry point for the application.
    * @param array $config User-defined options
    */
    public function __construct($config)
    {
        // Merge user config with defaults.
        $config = array_merge($this -> app_config_defaults, $config);

        // These initialization functions can throw errors. Catch them.
        try
        {
            $this -> DB = new DB(Config::load('database'));
            ClassRegistry::load('DB', $this -> DB);
        }
        // Remove FileNotFoundException??
        catch (MysqliException | FileNotFoundException $e)
        {
            exit($e); // Terminate with error messages.
        }

        // Initialize classes that won't throw errors.
        $this -> Router = new Router($config['route']);
        $this -> Router -> route();

    }

}
