<?php

/**
 * BaseModel.php
 *
 * This class is the parent of all Models. It has functionality that all other
 * models will use. It will also have properties that will need to be set by
 * each model, such as name, layout, etc.
 *
 * Once all parameters are passed, it will initialize (Connect to your DB table)
 * and allow you to manipulate.
 */

namespace Core;

use ErrorHandling\Exceptions\ModelCoreException;
use Util\ClassRegistry;

class BaseModel
{

    /**
     * Class registry object
     * @var \Util\ClassRegistry
     */
    protected $CR = null;

    /**
     * DB Object
     * @var \Core\DB
     */
    protected $DB = null;

    /**
     * Name of $this model
     * @var string
     */
    protected $name = null;

    /**
     * Name of the table this model is associated with.
     * @var string
     */
    protected $table_name = null;

    /**
     * Table layout for DB class create_table function.
     *
     * @see \Core\DB->create_table()
     * @var array
     */
    protected $layout = [];

    /**
     * Initializes the model and connects to the DB.
     */
    public function __construct()
    {
        $this->name = get_class($this);

        // Make sure the model has been initialized by the developer.
        if ($this->layout == [])
        {
            throw new ModelCoreException("Parent \Core\BaseModel->__construct should NOT be called before establishing model name and layout.", 1);
        }

        // Get our DB connection.
        $this->DB = ClassRegistry::load('DB');

        $name = explode('\\', $this->name);
        $name = strtolower(array_pop($name));
        $this->table_name = str_replace('model', '', $name);

        if (!$this->DB->table_exists($this->table_name))
        {
            $this->DB->create_table($this->table_name, $this->layout);
        }
    }

    /**
     * Inserts data into the table.
     *
     * Data should be formatted like so:
     *
     * $data = [
     *     'col_name' => 'value',
     *     'col_name2' => '2nd value',
     *     ...
     * ];
     *
     * @param  array $data The data to store
     * @return bool        Whether it completed.
     */
    public function insert($data)
    {
        // Set data cols and vals strings. Will be appended to
        $data_cols = "";
        $data_vals = "";

        // Construct the strings for columns and vals.
        foreach ($data as $col => $val)
        {
            $val = $this->parse_before($col, $val);

            $data_cols .= "{$col}, ";
            $data_vals .= "'{$val}', ";
        }

        // Remove comma and space from cols and vals, get table name
        $data_cols = substr($data_cols, 0, -2);
        $data_vals = substr($data_vals, 0, -2);
        $table = $this->table_name;

        // Format the string for querying
        $sql = sprintf("INSERT INTO `%s` (%s) \nVALUES (%s)",
            $table,
            $data_cols,
            $data_vals);

        // Returns only true or false, since we aren't fetching.
        return $this->DB->q($sql);
    }

    /**
     * Updates rows in the table
     * @param  array $data   Data to manipulate
     * @param  array $where  Data to test for
     * @return bool          Whether successful or not
     */
    public function update($data, $where)
    {
        // Initialize variables
        $data_str = "";
        $table = $this->table_name;

        // Format data
        foreach ($data as $col => $val)
        {
            $val = $this->parse_before($col, $val);

            $data_str .= "{$col}='{$val}', ";
        }

        // Cut off comma and space
        $data_str = substr($data_str, 0, -2);

        $sql = sprintf("UPDATE `%s` SET %s %s",
            $table, $data_str, $this->parse_where($where));

        return $this->DB->q($sql);
    }

    /**
     * Selects data from the table and returns an array full of all values.
     * @param  array  $fields    Fileds to search for. Pass empty array to select all
     * @param  array  $where     Array WHERE clause (@see \Core\BaseModel::parse_where())
     * @param  string $limit     SQL limit parameter
     * @param  array  $order_by  Used as ['<col>', '<ASC|DESC>']
     * @return array             Found data, or empty array
     */
    public function select(array $fields, $where, $limit = null, $order_by = null)
    {
        // default
        $field = "*";

        // Populate $field if array has values
        if (!empty($fields))
        {
            $field = "";

            // Loop through and add each one
            foreach ($fields as $f)
            {
                $field .= sprintf("`%s`, ", $f);
            }

            // Remove comma and space
            $field = substr($field, 0, -2);
        }

        // Construct the core query
        $sql = sprintf("SELECT %s FROM `%s` %s",
            $field, $this->table_name, $this->parse_where($where));

        // Check for limit condition
        if ($limit != null)
        {
            $sql .= " LIMIT {$limit}";
        }

        // Check for ORDER BY directive
        if ($order_by != null)
        {
            $order_by[1] = strtoupper($order_by[1]);
            $sql .= " ORDER BY `{$order_by[0]}` $order_by[1]";
        }

        // Fetch all of the data
        $data = $this->query_fetch_all($this->DB->q($sql));

        // Return false if no data is present, or the data if it is.
        //exit(var_dump($this->parse_where($where)));
        return (empty($data)) ? false : $data;
    }

    /**
     * Alias for @see \Core\BaseModel::select()
     * @param  array  $fields    Fields to search for. Pass empty array to select all
     * @param  array  $where     Array WHERE clause (@see \Core\BaseModel::parse_where())
     * @param  string $limit     SQL limit parameter
     * @param  array  $order_by  Used as ['<col>', '<ASC|DESC>']
     * @return array             Found data, or empty array
     */
    public function get(array $fields, $where, $limit = null, $order_by = null)
    {
        return $this->select($fields, $where, $limit, $order_by);
    }

    /**
     * Takes a query object and fetches all data, if any.
     * @param  \mysqli_result $q The executed query
     * @return array             The data
     */
    private function query_fetch_all($q)
    {
        $data = [];

        // Iterates through results and pushes them onto the array.
        while ($row = $q->fetch_assoc())
        {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Checks if a column's value should be processed in developer-defined
     * function to alter the value.
     *
     * @param  string $col Column to check
     * @param  string $val The value as a string
     * @return string      The altered value, or the original if no parsing needed
     */
    private function parse_before($col, $val)
    {
        // Checks if the child model wants to modify the data.
        if (method_exists($this, "before_{$col}"))
        {
            $func = "before_{$col}";

            $val = $this->$func($val);
        }

        return $val;
    }

    /**
     * Parses the where statements for a query.
     *
     * Where statements should be set up like so:
     * $where = [
     *     'col_name' => '[=,<,>,etc]<value>'
     * ];
     *
     * With combinational logic (example):
     * $where = [
     *     '_logic' => 'and', // Can also be OR
     *     'id' => '=1',
     *     'email' => '=josh.doe@ex.com'
     * ]; // Selects rows with id of 1 AND email of josh.doe@ex.com
     *
     * @param  array $where Where array
     * @return string       The section of SQL code with WHERE
     */
    private function parse_where($where)
    {
        $where_str = "WHERE ";

        // Test if $where '_logic' key is supplied.
        $logic = null;
        if (isset($where['_logic']))
        {
            $logic = strtoupper($where['_logic']);
            unset($where['_logic']);
        }

        // Parse where array
        foreach ($where as $col => $cond)
        {
            if (strtolower($cond[0]) == "like")
            {
                $op = " LIKE ";
            }
            else
            {
                $op = $cond[0];
            }

            $val = "'{$cond[1]}'";

            // Print differently if _logic key was supplied.
            if ($logic != null)
            {
                $where_str .= sprintf("`%s`%s%s %s ",
                    $col, $op, $val, $logic);
            }
            else
            {
                $where_str .= sprintf("`%s`%s%s",
                    $col, $op, $val);
            }
        }

        // Cut last AND, if logic was and.
        if ($logic == 'AND')
        {
            $where_str = substr($where_str, 0, -5);
        }

        // Cut last OR, if logic was or.
        if ($logic == 'OR')
        {
            $where_str = substr($where_str, 0, -4);
        }

        //echo $where_str;

        return $where_str;
    }

}
