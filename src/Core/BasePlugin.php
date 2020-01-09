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
    public $plugin_dir = null;

    /**
     * Stores name of the plugin
     * @var string
     */
    public $plugin_name = null;

    /**
     * If plugin has a config file, this stores all of that data. Only gets
     * filled if $this->load_config() runs correctly.
     * @var array
     */
    protected $config = [];

    /**
     * Models handler that will be specific to each plugin.
     * @var \Core\Models
     */
    protected $Model = null;

    /**
     * Gets the plugin name, establishes directory.
     */
    public function __construct()
    {
        // Get the name
        $name = explode('\\', get_class($this));
        $name = array_pop($name);
        $this->plugin_name = $name;

        // Establish plugin directory
        $this->plugin_dir = PLUGINS . $name . DS;
        //echo $this->plugin_dir;

        $this->loadLibs();

        $this->Model = new Models($this);
    }

    protected function load_config()
    {
        $path = $this->plugin_dir . 'config.inc.php';

        if (!file_exists($path))
        {
            throw new FileNotFoundException("File '{$path}' not found.", 404);
        }

        $this->config = include($path);
    }

    private function loadLibs()
    {
        if (isset($this -> libraries))
        {
            foreach ($this -> libraries as $lib => $args)
            {
                if ($this->lib_exists($lib))
                {
                    require($this->plugin_dir . '/lib/' . $lib . '.php');
                    $ns_lib = "\\Plugins\\{$this -> plugin_name}\\Libs\\{$lib}";
                    $this -> $lib = new $ns_lib($args);
                }
            }
        }
    }

    private function lib_exists($lib)
    {
        return file_exists($this->plugin_dir . '/lib/' . $lib . '.php');
    }

}
