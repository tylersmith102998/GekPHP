<?php

namespace Core;

use ErrorHandling\Exceptions\ControllerNotFoundException;

class Router
{

    public $_404 = false;

    /**
     * Name of the controller to use. Assigned in Core\Router::breakDownRoute();
     * @var string
     */
    private $controllerName = null;

    /**
     * Name of the method to use. Assigned in Core\Router::breakDownRoute();
     * @var string
     */
    private $methodName = null;

    /**
     * Arguments to pass to the controller and method. Assigned in Core\Router::breakDownRoute();
     * @var array
     */
    public $args = [];

    public $route = null;

    /**
     * Initializes the router to get us to the correct page.
     * @param string $route [description]
     *
     * @TODO: Modify catch block to handle if controller was not found. (Redirect to 404 page or something similar)
     */
    public function __construct(string $route)
    {
        $this->route = $route;
        try {
            $this->breakDownRoute($route);
        } catch (ControllerNotFoundException $e) {
            exit ($e);
        }
    }

    /**
     * Returns the page info of the route given when class was created.
     * @return Core\Controller The desired page.
     */
    public function route()
    {
        $controller = "\\Controllers\\" . $this->controllerName;
        $method = $this->methodName;
        $args = $this;

        $C = new $controller($method, $args);
        $C->$method();
    }

    /**
     * Breaks down a string URI to get Controller, Method, & Args
     * @param  string $route The route (ie. home/index)
     * @return void
     *
     * @throws ErrorHandling\Exceptions\ControllerNotFoundException if controller or method do not exist.
     *
     * @TODO: Convert any numbers to a string with underscore. ie. 404 => _404.
     */
    private function breakDownRoute($route)
    {
        // Turn string route into an array around the '/'
        $route = explode("/", $route);

        // Get name of the controller, always first value of route.
        $controller = ucfirst($route[0]);
        unset($route[0]); // Empties index 0
        $route = array_values($route); // Get rid of empty cell. index 1 becomes 0.

        if ($controller == '')
        {
            $controller = "Home";
        }

        $controller .= 'Controller';

        if (!$this->checkControllerExists($controller))
        {
            //throw new ControllerNotFoundException("Controller '{$controller}.php' not found in " . CONTROLLERS, 404);
            $controller = "HomeController";
            $this->_404 = true;
        }

        // Check for method name. if set, assign it. If not, 'index' is used.
        // Always 2nd value of route.
        if (isset($route[0]))
        {
            $method = ucfirst($route[0]);
            unset($route[0]);
            $route = array_values($route); // See above;
        }
        else
        {
            $method = 'Index'; // Default method.
        }

        if (!$this -> checkControllerMethodExists($controller, $method))
        {
            $method = "Index";
            $this->_404 = true;
            //throw new ControllerNotFoundException("Method {$method} is not a member of controller {$controller}", 405);
        }

        // Check for args, if so, use them. If not, empty array.
        if (isset($route[0]))
        {
            $args = $route;
        }
        else
        {
            $args = [];
        }

        // Assign the values to the class properties.
        $this->controllerName = $controller;
        $this->methodName = $method;
        $this->args = $args;
    }

    /**
     * Returns the result of file_exists() for a given controller name.
     * @param  string $controller Name of the controller
     * @return bool               Whether or not it exists
     */
    private function checkControllerExists($controller)
    {
        return file_exists(CONTROLLERS . $controller . '.php');
    }

    /**
     * Checks the controller to ensure the selected method exists. If not, returns false.
     * @param  string $controller Controller name
     * @param  string $method     Method name
     * @return bool               Whether or not controller exists
     */
    private function checkControllerMethodExists($controller, $method)
    {
        require_once(CONTROLLERS . $controller . '.php');

        return method_exists("Controllers\\" . $controller, $method);
    }

}
