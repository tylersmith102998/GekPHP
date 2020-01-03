<?php

/**
 * Models.php
 *
 * This class is responsible for handling everything to do with Models.
 *
 * Models in this framework represent a database table, and all models will extend
 * the \Core\BaseModel class, which provides data manipulation functions for
 * selecting, modifying, or inserting data. This class is simply a factory for
 * loading in models and storing their objects in a private array.
 *
 * @author Tyler A. Smith
 *
 * @since 0.0.1 Introduced
 */

namespace Core;

use ErrorHandling\Exceptions\ModelNotFoundException;

class Models
{

    /**
     * Associative array of models that have been loaded in.
     * @var array
     */
    private $loaded_models = [];

    /**
     * Loads in a Model and spits it's object back for immediate use.
     *
     * If already loaded, it will simply return the object.
     *
     * @param  string           $name Model name
     * @return \Core\BaseModel  The model object
     */
    public function load(string $name)
    {
        // Capitalize first char
        $name = ucfirst($name . 'Model');

        // Check if file exists
        if (!file_exists(MODELS . $name . '.php'))
        {
            // Throw error if file not found.
            throw new ModelNotFoundException("Model '{$name}.php' not found. PHP File is missing.", 404);
        }

        // Checks if model is loaded or not.
        if (!isset($this->loaded_models[$name]))
        {
            // Load Model class file
            require_once(MODELS . $name . '.php');

            // Construct class name w/ namespacing.
            // All models should use namespace 'Models'
            $nsname = "\\Models\\" . $name;

            // Loads class into memory.
            $this->loaded_models[$name] = new $nsname();
        }

        return $this->loaded_models[$name];
    }

}
