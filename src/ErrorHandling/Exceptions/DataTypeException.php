<?php

namespace ErrorHandling\Exceptions;

use ErrorHandling\BaseException;

/**
 * Custom Exception Handler for incorrect data types.
 */
class DataTypeException extends BaseException
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
