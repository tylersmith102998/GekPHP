<?php

require_once('config/bootstrap.php');

use Config\Config;
use Core\App;

new App(Config::load('app_config.php'));
