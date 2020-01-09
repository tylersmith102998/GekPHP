<?php

namespace Plugins\Auth\Libs;

class Conf
{

    private $config = [];

    public function __construct(){}

    public function init($config)
    {
        $this->config = $config;
    }

    public function get($path)
    {
        $path = explode('.', $path);
        $config = $this->config;

        foreach ($path as $c)
        {
            if (isset($config[$c]))
            {
                $config = $config[$c];
            }
        }

        return $config;
    }

}
