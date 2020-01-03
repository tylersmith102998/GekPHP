<?php

namespace ErrorHandling\Exceptions;

use ErrorHandling\Exceptions\FileNotFoundException;

/**
 * Custom Exception Handler for Model location issues.
 */
class ModelNotFoundException extends FileNotFoundException
{

    /**
    * Called when exception is thrown to give message, code, & prev Exception.
    * @param string      $message  Error message
    * @param integer     $code     Error code
    * @param \Exception  $previous Previous Exception
    */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
