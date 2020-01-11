<?php

/**
 * Matrix.php
 *
 * This class represents a mathematical matrix and implements tools to perform
 * quick matrix math on the fly.
 */

namespace Math;

class Matrix
{

    /**
     * Holds x and y size variables in array format. x is row count, y is col.
     * @var array
     */
    public $size = [];

    /**
     * Holds a transposed matrix array.
     * @var array
     */
    public $T;

    /**
     * Holds this matrix array.
     * @var array
     */
    private $matrix_arr;

    /**
     * Holds the row count for this matrix.
     * @var int
     */
    private $rows;

    /**
     * Holds the col count for this matrix.
     * @var int
     */
    private $cols;

    /**
     * Initializes the matrix.
     * @param array $rows matrix array.
     */
    public function __construct(...$rows)
    {
        $this->matrix_arr = $rows[0];
        $this->rows = count($this->matrix_arr);

        $max_col = 0;
        for ($i = 0; $i < $this->rows; $i++)
        {
            $col = count($this->matrix_arr);

            if (isset($this->matrix_arr[$i]) && is_array($this->matrix_arr[$i]))
            {
                $col = count($this->matrix_arr[$i]);
            }

            if ($col > $max_col)
            {
                //var_dump($max_col);
                $max_col = $col;
            }
        }

        $this->cols = $max_col;
        $this->size['x'] = $this->rows;
        $this->size['y'] = $this->cols;

        $this->T = $this->transpose();

        return $this;
    }

    /**
     * Adds values from one matrix to another. Matrices must be of the same size.
     * @param \Math\Matrix $Matrix matrix to add to this one.
     * @return this
     */
    public function add(\Math\Matrix $Matrix)
    {
        $this->check_same_size($Matrix);

        $result = [];
        for ($i = 0; $i < $this->rows; $i++)
        {
            $result[$i] = [];

            for ($j = 0; $j < $this->cols; $j++)
            {
                $result[$i][$j] = $this->get_index($i, $j) + $Matrix->get_index($i, $j);
            }
        }

        $this->matrix_arr = $result;
        $this->T = $this->transpose();
        return $this;
    }

    /**
     * Subtracts a matrix of values from this one. Matrices must be of the same size.
     * @param  \Math\Matrix $Matrix the matrix to subtract from this one
     * @return this
     */
    public function sub(\Math\Matrix $Matrix)
    {
        $this->check_same_size($Matrix);

        $result = [];
        for ($i = 0; $i < $this->rows; $i++)
        {
            $result[$i] = [];

            for ($j = 0; $j < $this->cols; $j++)
            {
                $result[$i][$j] = $this->get_index($i, $j) + $Matrix->get_index($i, $j);
            }
        }

        $this->matrix_arr = $result;
        $this->T = $this->transpose();
        return $this;
    }

    /**
     * Scales the matrix by a scalar (double) value.
     * @param  double $scalar Amount to scale the values by.
     * @return this
     */
    public function scale($scalar)
    {
        $result = [];
        for ($i = 0; $i < $this->rows; $i++)
        {
            $result[$i] = [];

            for ($j = 0; $j < $this->cols; $j++)
            {
                $result[$i][$j] = $this->get_index($i, $j) * $scalar;
            }
        }

        $this->matrix_arr = $result;
        $this->T = $this->transpose();
        return $this;
    }

    /**
     * Gets the dot product matrix and returns as a new matrix.
     * @param  \Math\Matrix $Matrix matrix to multiply by. Must follow standard matrix multiplication rules.
     * @return \Math\Matrix         matrix with the dot products.
     */
    public function dot(\Math\Matrix $Matrix)
    {
        $r = $this->rows;
        $c = $Matrix->cols;
        $p = $Matrix->rows;
        if($this->cols != $p){
            throw new MathException('Incompatible Matrices. Unable to obtain dot product.');
        }

        $result=array();

        for ($i=0; $i < $r; $i++){
            for($j=0; $j < $c; $j++){
                $result[$i][$j] = 0;
                for($k=0; $k < $p; $k++){
                    $result[$i][$j] += $this->get_index($i,$k) * $Matrix->get_index($k,$j);
                }
            }
        }

        return new Matrix($result);
    }

    /**
     * Gets an index from this matrix.
     * @param  int      $i matrix row
     * @param  int      $j matrix column
     * @return mixed       value of the index of this matrix.
     */
    public function get_index($i, $j)
    {
        if (isset($this->matrix_arr[$i][$j]))
        {
            return $this->matrix_arr[$i][$j];
        }
        else
        {
            return 0;
        }
    }

    /**
     * Gets a particular matrix row as an array.
     * @param  int    $i matrix row
     * @return array     values at that matrix row
     */
    public function get_row($i)
    {
        if (isset($this->matrix_arr[$i]))
        {
            return $this->matrix_arr[$i];
        }
        else
        {
            throw new MathException("Attempted to fetch row index #{$i}, but it did not exist.");
        }
    }

    /**
     * Gets a particular matrix column as an array.
     * @param  int   $j matrix column.
     * @return array    values at that matrix column
     */
    public function get_col($j)
    {
        $ret = [];

        foreach ($this->matrix_arr as $row => $col)
        {
            if (isset($this->matrix_arr[$row][$col]))
            {
                $ret[] = $this->matrix_arr[$row][$col];
            }
        }

        return $ret;
    }

    /**
     * Spits back the entire matrix array.
     * @return array the matrix.
     */
    public function get_matrix()
    {
        return $this->matrix_arr;
    }

    /**
     * Returns Matrix size parameters to the calling function.
     * @return array matrix size ['x', 'y']
     */
    public function get_size()
    {
        return $this->size;
    }

    /**
     * Returns the matrix size as a string <rows>x<cols>
     * @return [type] [description]
     */
    public function get_size_string()
    {
        return sprintf("%sx%s", $this->size['x'], $this->size['y']);
    }

    /**
     * Prints out the matrix as neatly as possible.
     * @return void
     */
    public function print()
    {
        $arr = $this->matrix_arr;
        //var_dump($arr);

        $html = "<pre>\\Math\\Matrix ({$this->get_size_string()}):\n";

        for ($i = 0; $i < $this->rows; $i++)
        {
            for ($j = 0; $j < $this->cols; $j++)
            {
                $val = 0;

                if (isset($arr[$i][$j]))
                {
                    $val = $arr[$i][$j];
                }
                //var_dump($val);
                $html .= "[{$val}]\t";
            }

            $html .= "\n";
        }

        echo $html . "</pre>";
    }

    /**
     * Static function to generate and return a matrix with completely random
     * float values. By default, it will generate with randome values between
     * 0.0 and 1.0
     *
     * @param  int           $rows     how many rows the matrix should have
     * @param  int           $cols     how many columns the matrix should have
     * @param  double        $rand_min minimum random value
     * @param  double        $rand_max maximum random value
     * @return \Math\Matrix            matrix generated with the random values.
     */
    public static function generate_random($rows, $cols, $rand_min = 0, $rand_max = 1)
    {
        $precision = 1000;
        $rand_max *= $precision;
        $rand_min *= $precision;

        $arr_matrix = [];
        for ($i = 0; $i < $rows; $i++)
        {
            $row = [];

            for ($j = 0; $j < $cols; $j++)
            {
                $row[] = mt_rand($rand_min, $rand_max) / $precision;
            }

            $arr_matrix[] = $row;
        }

        //var_dump($arr_matrix);
        return new self($arr_matrix);
    }

    /**
     * Transposes the matrix.
     * @return array the transposed matrix as an array.
     */
    public function transpose()
    {
        $arr = $this->matrix_arr;

        return array_map(null, ...$arr);
    }

    /**
     * Checks this matrix with the given matrix and makes sure they are exactly
     * the same size. Throws an error if it doesn't pass.
     *
     * @param  \Math\Matrix $Matrix the matrix to test
     * @return void
     */
    private function check_same_size(\Math\Matrix $Matrix)
    {
        if ($this->cols != $Matrix->cols || $this->rows != $Matrix->rows)
        {
            throw new MathException('You cannot add two matrices of different sizes.');
        }
    }

}
