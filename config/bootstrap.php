<?php

/**
 * /config/bootstrap.php
 *
 * This file is responsible for setting constant variables for use throughout
 * the application.
 *
 * @author Tyler A. Smith
 * @copyright 2020 Tyler A. Smith
 * @since 0.0.1
 */

 /**
  * Loads classes from the src folder using namespaces that correlate to the
  * directory structure. Throws an error if the class wasn't found and prints
  * the name of the calling function.
  */
 spl_autoload_register(function($class) {
     // Replaces backslashes with DS, sets error state to false.
     $file = str_replace('\\', DS, $class);
     $error = 0;

     // Attempt to load the resource.
     try
     {
         if (!include(SRC . $file . '.php'))
             throw new \Exception('Class "' . $file . '" does not exist.');
     }
     catch (\Exception $e)
     {
         // Error state tripped
         $error = 1;

         // Get calling function information from debug_backtrace().
         $debug_trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2)[1];
         $file = $debug_trace['file'];
         $line = $debug_trace['line'];
         $function = $debug_trace['function'];

         // Spit the error.
         echo "Caught error: " . $e -> getMessage() . "\n\n"
             . "<b>" . $file . ":" . $line . "</b>\n\n"
             . "Called by: " . $function . "\n\n";
     }
     finally
     {
         // Triggers exit if error was present to prevent further execution.
         if ($error) exit();
     }
 });

use Networking\Request;
use Util\ClassRegistry;

// Directory Separator
define('DS', '/');

//Takes the root directory, and corrects "\" to "/" if running on a Windows system
define('ROOT', str_replace('\\', '/', dirname(__DIR__)) . DS);

// Config folder
define('CONFIG', ROOT . 'config' . DS);

// PHP Source Code (Framework)
define('SRC', ROOT . 'src' . DS);

// MVC Resources are located here.
define('WWW', ROOT . 'www' . DS);

// Plugins will go here. Refer to parent plugin source code for how to implement 
// and build a custom plugin
define('PLUGINS', ROOT . 'Plugins' . DS);

// Controllers
define('CONTROLLERS', WWW . 'Controllers' . DS);

// Models
define('MODELS', WWW . 'Models' . DS);

// Views
define('VIEWS', WWW . 'Views' . DS);

// Load in top-level classes to the registry. Some are assigned for use within
// bootstrap.php
$R = ClassRegistry::load('Request', new Request());

// Grab the URI
$uri = "";
if ($R->get('r'))
{
    $uri = $R->get('r');
}
define('URI', $uri);
