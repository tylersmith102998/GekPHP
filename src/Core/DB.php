<?php

/**
 * This class handles all of the database interactions the application makes.
 * All models and model extensions will use this in their highest parent class.
 */

namespace Core;

use ErrorHandling\Exceptions\DBException;
use ErrorHandling\Exceptions\MysqliException;

class DB
{

    private $default_column_params = [
        'data_type' => 'INT',
        'null' => false,
        'default' => null,
        'auto_inc' => false
    ];

    /**
     * The mysqli connection var. Is set in Core\DB::__construct();
     * @var \mysqli
     */
    private $con = null;

    /**
     * Pull info from config and make a connection.
     * @param array $config Database connection settings
     */
    public function __construct(array $config)
    {
        // Create mysqli object.
        $this->con = new \mysqli($config['host'],
            $config['username'],
            $config['password'],
            $config['database']);

        // Check for connectivity errors.
        if ($this->con->connect_errno)
        {
            // Throw connection error.
            throw new MysqliException($this->con->connect_error, 1);
        }

        return true; // Only returns this if connection was successful.
    }

    /**
     * Checks the database to see if a table exists.
     *
     * @param  string $name Table name to test
     * @return bool         True if table exists, false if not.
     * @see https://stackoverflow.com/questions/6432178/how-can-i-check-if-a-mysql-table-exists-with-php
     */
    public function table_exists($name)
    {
        $sql = "SELECT 1 FROM `{$name}` LIMIT 1";
        $q = $this->con->query($sql);
        return $q;
    }

    /**
     * Creates a table in the database based off of passed parameters.
     *
     * Array for columns should be constructed as follows:
     * [ ...
     *     "col_name" => [
     *         "data_type" => "(ie. VARCHAR(64))"
     *         "null" => True | False
     *         "default" => Default value for each row inserted
     *         "auto_inc" => True | False
     *     ],
     *     "_primary_keys" => [
     *         "col_1",
     *         "col_2",
     *         ...
     *     ],
     * ... ]
     *
     * Defaults:
     *      data_type: INT
     *      null: false
     *      default: null (No default)
     *      auto_inc: false
     *
     * @param  string $name  Table name
     * @param  array  $cols  Column layout. @see Core\DB->create_table() for further info
     * @return bool          True if table created. False if not.
     */
    public function create_table($name, $cols, $ignoreCheck = true)
    {
        // Checks if table already exists and spits error if dev wants to
        // prevent further action by default. Ignores this if ignoreCheck is
        // true.
        if ($this->table_exists($name) && !$ignoreCheck)
        {
            throw new MysqliException("Table '{$name}' already exists in the database.");
        }

        if ($this->table_exists($name))
        {
            return true;
        }

        // Grab primary keys from cols array
        $primaryKeys = $cols['_primary_keys'];
        unset($cols['_primary_keys']);

        // Beginning of the SQL query.
        $sql = "CREATE TABLE IF NOT EXISTS {$name} (\n";

        // Attach all of the columns.
        foreach ($cols as $col => $params)
        {
            // Merge defaults.
            $params = array_merge($this->default_column_params, $params);

            $sql .= "\t{$col} {$params['data_type']} ";

            // Check if column should be null
            if (!$params['null'])
                $sql .= "NOT NULL ";

            // Check if column has default val
            if ($params['default'] != null)
                $sql .= "DEFAULT '{$params['default']}' ";

            // Check if col should auto increment.
            if ($params['auto_inc'])
                $sql .= "AUTO_INCREMENT ";

            // Trim excess space
            $sql = substr($sql, 0, -1) . ",\n";
        }

        // Begin listing of primary key.
        $sql .= "\tPRIMARY KEY (";

        // Sets each col that belongs to primary key.
        foreach ($primaryKeys as $key)
        {
            $sql .= $key . ",";
        }

        // Trim excess comma.
        $sql = substr($sql, 0, -1) . ")\n";

        // Finished query string
        $sql .= ") ENGINE=INNODB;";

        //echo $sql;

        try
        {
            $q = $this->q($sql);
            return true;
        } catch (DBException $e)
        {
            exit($e);
        }
    }

    /**
     * Alias for performing a mysqli query. Throws a DBException if error.
     * @param  string   $sql The SQL query to be performed.
     * @return mixed    \mysqli_result when successful, true for success, or false for failure.
     * @throws ErrorHandling\Exceptions\DBException when the server has an issue with the query
     */
    public function q($sql)
    {
        //exit($sql);
        // Perform query using mysqli.
        $q = $this->con->query($sql);

        // Check for errors, throw exception with error if found.
        if ($this->con->errno)
        {
            throw new DBException("Query error: [{$this->con->errno}] {$this->con->error}\nSQL Query: {$sql}", 1);
        }

        // Returns the Mysqli query object.
        return $q;
    }

}
