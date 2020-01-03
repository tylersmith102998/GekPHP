<?php

namespace Controllers;

use ErrorHandling\Exceptions\FileNotFoundException;

class HomeController extends \Core\BaseController
{

    private $U = null;

    public function __construct($method, $args)
    {
        parent::__construct($method, $args);

        try {
            $this->U = $this->Model->load('users');
            $this->A = $this->Plugin->load('Auth');
        } catch (FileNotFoundException $e) {
            exit($e);
        }
    }

    public function Index()
    {
        $data = [
            'email' => 'testssssssssss@test.org',
            'password' => 'oofoffoff'
        ];

        $where = [
            'email'  => ['like', '%tedst@%'],
        ];

        $this->test_var = 'mytext';

        print_r($this->U->select([], $where, null, ['id', 'desc']));

        $this->view();
    }

    public function About($args)
    {
        echo 'This is the about page';
    }

}
