<?php

namespace Controllers;

use ErrorHandling\Exceptions\FileNotFoundException;

class HomeController extends \Core\BaseController
{

    public function __construct($method, $args)
    {
        parent::__construct($method, $args);
    }

    // Index method is always ucfirst.
    public function Index() // /home/index
    {
        // Pre-process data.

        $this->view();
    }

    public function mysecondpage() // /home/mysecondpage
    {
        // Pre-process data.

        $this->view();
    }

}
