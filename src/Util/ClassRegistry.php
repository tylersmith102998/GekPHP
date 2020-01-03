<?php

/**
 * ClassRegistry.php
 *
 * This class is responsible for re-using singular objects throughout the entire
 * application. Uses the singleton programming pattern to ensure only one
 * instance of the class is initialized.
 *
 * @since 0.0.1
 * @author Tyler A. Smith
 */

namespace Util;

class ClassRegistry
{

    /**
     * The instance of this registry object.
     * @var Util\ClassRegistry
     */
    private static $instance = null;

    /**
     * Array of objects to be stored within the registry object.
     * @var array
     */
    private $objects = [];

    /**
     * Loads an object into the registry.
     * @param  string $class_name Class name
     * @param  object $obj        The object. (Use 'new' keyword in most cases unless passing in an existing object.) Can be null to pull existing object.
     * @return object             The stored object.
     */
    public static function load($class_name, $obj = null)
    {
        $i = static::getInstance();

        // If $obj is null, attempt to get from existing object batch.
        if ($obj == null)
        {
            return $i->get($class_name);
        }

        return $i->store($class_name, $obj);
    }

    /**
     * Another way of using load without passing a object.
     * @param  [type] $class_name [description]
     * @return [type]             [description]
     */
    public static function getObj($class_name)
    {
        return static::load($class_name);
    }

    /**
     * Private constructor ensures that class can only initialize itself.
     */
    private function __construct() {}

    /**
     * Stores an object in the instance of registry.
     * @param  string $name Class name
     * @param  object $obj  Object to store
     * @return object       Object that was stored.
     */
    private function store($name, $obj)
    {
        $this->objects[$name] = $obj;
        return $obj;
    }

    /**
     * Allows the instance of registry to get an object.
     * @param  string $name Class name.
     * @return mixed        False if not found, The object if it is.
     */
    private function get($name)
    {
        if (isset($this->objects[$name]) && is_object($this->objects[$name]))
        {
            return $this->objects[$name];
        }

        return false;
    }

    /**
     * This private static function will be used by all public method calls to
     * grab the registry object.
     * @return Util\ClassRegistry object.
     */
    private static function getInstance()
    {
        if (static::$instance == null)
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

}
