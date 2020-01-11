<?php

/**
 * Auth\Account library.
 *
 * This library class represents an account. The constructor takes all of the
 * values part of the array $account and turns them into class properties.
 *
 * This class also implements functionality for modifying and parsing account
 * data on the go, and you can use one function to return all the changed
 * properties of the class.
 */

namespace Plugins\Auth\Src;

class Account
{

    /**
     * Converts all array indexes to class properties.
     * @param array $account the account information to mold the object.
     * @return this
     */
    public function __construct(array $account)
    {
        foreach ($account as $prop => $val)
        {
            $this -> $prop = $val;
        }

        return $this;
    }

}
