<?php

namespace Plugins\Auth\Models;

class SessionsModel extends \Core\BaseModel
{

    public function __construct($layout)
    {
        $this->layout = $layout;

        parent::__construct();
    }

}
