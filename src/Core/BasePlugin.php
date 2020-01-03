<?php

/**
 * BasePlugin.php
 *
 * This class is the parent of all plugins that exist and helps to unify them.
 */

namespace Core;

class BasePlugin
{

    /**
     * Directory where we can find a plugin's other source code. This is set
     * automatically based on the name of the plugin (Plugin name == folder
     * name)
     * @var string
     */
    protected $plugin_dir = null;

    /**
     * Gets the plugin name, establishes directory.
     */
    public function __construct()
    {
        // Get the name
        $name = explode('\\', get_class($this));
        $name = array_pop($name);

        // Establish plugin directory
        $this->plugin_dir = PLUGINS . $name . DS;
        //echo $this->plugin_dir;
    }

}
