<?php

/**
 * Controller.php
 *
 * This is the core controller. Contains lots of information about the current
 * controller.
 */

namespace Core;

use \Networking\Cookie;
use \Networking\Session;

class Controller
{

    /**
     * Name of the controller
     * @var string
     */
    protected $controller_name = null;

    /**
     * Model object
     * @var \Core\Model
     */
    protected $Model = null;

    /**
     * Handler class for plugin logic.
     * @var \Core\Plugin
     */
    protected $Plugin = null;

    /**
     * Handler class for PHP sessions
     * @var \Networking\Session
     */
    protected $Session = null;

    /**
     * Handler class for PHP cookies
     * @var \Networking\Cookie
     */
    protected $Cookie = null;

    /**
     * Initialize controller
     * @param string $name Controller name
     */
    public function __construct()
    {
        $this->controller_name = explode('\\', get_class($this))[1];
        $this->Model = new Models();
        $this->Plugin = new Plugins();
        $this->Session = new Session();
        $this->Cookie = new Cookie();
    }

}
