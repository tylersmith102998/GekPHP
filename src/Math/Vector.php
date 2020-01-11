<?php

/**
 * Vector.php
 *
 * This class represents a mathematical vector. Its length and dimension is
 * determined by the number of parameters you pass to it.
 */

namespace Math;

use ErrorHandling\Exceptions\MathException;

class Vector
{

    /**
     * Holds the transposed vector. Can be used to help when multiplying a matrix.
     * @var array
     */
    public $T;

    /**
     * Holds the array vector data.
     * @var array
     */
    public $vect;

    /**
     * Holds the length of this vector.
     * @var int
     */
    public $length;

    /**
     * Initializes values and sets the properties of this vector.
     * @param [type] $values [description]
     */
    public function __construct(...$values)
    {
        $this->vect = $values[0];
        $this->length = count($values[0]);
        $this->T = $this->transpose();
        //var_dump($values);
        //exit();
    }

    /**
     * Adds a given vector to this vector. Vectors must both be of the same length or size.
     * @param \Math\Vector $Vector The vector to add to this one.
     * @return this
     */
    public function add(\Math\Vector $Vector)
    {
        $this->check_same_size($Vector);
        //exit(var_dump($this->length));
        //exit(var_dump($this->get(0)));

        $out = [];
        for ($i = 0; $i < $this->length; $i++)
        {
            $out[$i] = $this->get($i) + $Vector->get($i);
        }
        $this->vect = $out;
        $this->T = $this->transpose();

        return $this;
    }

    /**
     * Subtracts a vector from this one.
     * @param  \Math\Vector $Vector The vector to subtract by.
     * @return this
     */
    public function sub(\Math\Vector $Vector)
    {
        $this->check_same_size($Vector);

        $out = [];
        for ($i = 0; $i < $this->length; $i++)
        {
            $out[$i] = $this->get($i) - $Vector->get($i);
        }
        $this->vect = $out;
        $this->T = $this->transpose();

        return $this;
    }

    /**
     * Scales this vector by a scalar amount (1-dimensional vector)
     * @param  double $scalar value to scale the vector by.
     * @return this
     */
    public function scale($scalar)
    {
        $out = [];
        for ($i = 0; $i < $this->length; $i++)
        {
            $out[$i] = $this->get($i) * $scalar;
        }
        $this->vect = $out;
        $this->T = $this->transpose();

        return $this;
    }

    /**
     * Gets the dot product of multiplying this vector with another. Vectors must
     * be of the same size.
     *
     * @param  \Math\Vector $Vector vector to multiply with
     * @return double               value of the dot product
     */
    public function dot(\Math\Vector $Vector)
    {
        $this->check_same_size($Vector);

        $out = 0;
        for ($i = 0; $i < $this->length; $i++)
        {
            $out += $this->get($i) * $Vector->get($i);
        }

        return $out;
    }

    /**
     * Use this to get a specific value from the vector. If $i is passed, it must
     * be an integer, and is equivalent to the array index for the vector. If $i
     * is not passed, method will return the array-level vector.
     *
     * @param  int          $i (optional) the index of the vector array
     * @return double|array    value at that index
     */
    public function get($i = null)
    {
        if ($i === null)
        {
            return $this->vect;
        }
        else
        {
            if (isset($this->vect[$i]))
            {
                return $this->vect[$i];
            }
            else
            {
                throw new MathException('Index {$i} of vector was not found.', 2);
            }
        }
    }

    /**
     * Prints this vector out in a neat fashion.
     * @return void
     */
    public function print()
    {
        $str = "<pre>\\Math\\Vector:\n";

        for ($i = 0; $i < $this->length; $i++)
        {
            $str .= "[{$this->get($i)}]\n";
        }

        echo $str . "</pre>";
    }

    public function printT()
    {
        $str = "<pre>\\Math\\Vector (Transpose):\n";

        for ($i = 0; $i < $this->length; $i++)
        {
            $str .= "[{$this->get($i)}]\t";
        }

        echo $str . "</pre>";
    }

    /**
     * Checks a given vector with this one and throws an exception if they are
     * not of the same length/dimension/size
     *
     * @param  \Math\Vector $Vector vector to compare size with
     * @return void
     */
    private function check_same_size(\Math\Vector $Vector)
    {
        if ($this->length != $Vector->length)
        {
            throw new MathException("You cannot add two vecors of different length.", 1);
        }
    }

    /**
     * Performs a transpose on the current vector. Method is private because
     * transposition is done when the vector is created or updated.
     *
     * @return array transposed vector
     */
    private function transpose()
    {
        $out = [];
        for ($i = 0; $i < $this->length; $i++)
        {
            $out[$i] = [$this->get($i)];
        }

        return $out;
    }

}
