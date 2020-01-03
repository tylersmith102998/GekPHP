<?php

/**
 * Plugins.php
 *
 * This class is responsible for handling all of the plugin logic. Stores,
 * creates, and destroys plugin instances.
 *
 * @since 0.0.1
 * @author Tyler A. Smith
 */

namespace Core;

use ErrorHandling\Exceptions\PluginNotFoundException;

class Plugins
{

    /**
     * Container for all plugin objects.
     * @var array(\Core\BasePlugin)
     */
    private $Plugins = [];

    /**
     * Loads in a plugin and spits it's object back for use.
     * @param  string $name      Case-sensitive name of the plugin. Don't include '.php'
     * @param  array  $args      Arguments that need to be passed to the plugin. (ie. ['arg1', 'arg2', ...])
     * @return \Core\BasePlugin  The plugin object
     */
    public function load($name, array $args = [])
    {
        // Set the path
        $path = PLUGINS . $name . '.php';

        // Make sure file exists.
        if (!file_exists($path))
        {
            // If file not found:
            throw new PluginNotFoundException("Plugin '{$path}' not found. PHP file seems to be missing.", 404);
        }

        // Checks if plugin was already loaded, returns it if so
        if (isset($this->Plugins[$name]))
        {
            return $this->Plugins[$name];
        }

        // Bring it in
        require_once($path);
        $name = "Plugins\\" . $name;

        // Initialize and return the object.
        $this->Plugins[$name] = new $name($args);
        return $this->Plugins[$name];
    }

}
