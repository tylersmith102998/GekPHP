<?php

/**
 * BaseController.php
 *
 * This class is essentially a skelton for what all controllers should have
 * available to them. Contains useful functions and properties to be used with
 * controllers for debugging, templating, and more.
 */

namespace Core;

use ErrorHandling\Exceptions\ViewNotFoundException;

class BaseController extends Controller
{

    protected $view_path = null;

    /**
     * Initizlizes the controller
     * @param Core\Controller $obj The controller object.
     */
    public function __construct($method, $args)
    {
        $controller_name = get_class($this);

        $this->view_path = strtolower(
            str_replace(['Controllers\\', 'Controller'], '', $controller_name) .
            DS . strtolower($method));
        $this->args = $args;

        parent::__construct();

        // Load in plugins optionally to the whole site.
        // To get this functionality on a per-controller basis, copy-paste the
        // code below to the controller in question.
        try {
            $this->Auth = $this->Plugin->load('Auth');
        } catch (FileNotFoundException $e) {
            exit($e);
        }
    }

    /**
     * Loads in the view file. Uses path based on controller/method combo as
     * default if no path is supplied. Can be used to load in templates as well.
     *
     * @param  string $path Path to load reasource from. .php not neccessary.
     * @return void
     */
    protected function view($path = null)
    {
        $args = $this->args;

        // Use default if none set
        if ($path == null)
        {
            $path = $this->view_path;
        }

        // Carefully check to ensure file is actually there.
        try {
            if (!file_exists(VIEWS . $path . '.php'))
            {
                throw new ViewNotFoundException("View at path '{$path}' not found. PHP file missing.", 404);
            }
        } catch (ViewNotFoundException $e) {
            exit($e);
        }

        // Bring it in.
        require_once(VIEWS . $path . '.php');
    }

    /**
     * Alias for @see \Core\BaseController::view()
     *
     * @param  string $path Path where resource is located. .php not neccessary.
     * @return void
     */
    protected function t($path)
    {
        $this->view($path);
    }

}
