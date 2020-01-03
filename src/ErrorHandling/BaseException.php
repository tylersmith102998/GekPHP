<?php

/**
 * All exception classes will extend this one. This will be used so that
 * we can do a debug_backtrace() and figure out where an error occured in the
 * code.
 */

namespace ErrorHandling;

class BaseException extends \Exception
{

    /**
     * Filename in which the error occured.
     * @var string
     */
    protected $file = null;

    /**
     * Line number where the error occured.
     * @var integer
     */
    protected $line = null;

    /**
     * Name of the function in which the error occured.
     * @var string
     */
    protected $function = null;

    /**
     * The debug_backtrace for purposes of viewing the stack.
     * @var array
     */
    protected $trace = [];

    /**
     * Calls parent constructor and grabs line in which error occured.
     * @param string        $message  Error message
     * @param integer       $code     Error code
     * @param \Exception    $previous Previous exception.
     */
    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $this->file = $trace[1]['file'];
        $this->line = $trace[1]['line'];
        $this->function = $trace[2]['function'];

        $this->trace = array_reverse($trace);

    }

    public function __toString()
    {
        $html = "<b>" . get_class($this) .
            " [{$this->code}]:</b> {$this->message} " .
            "<b>[{$this->file}:{$this->line}] Function: {$this->function}</b>" .
            "\n\n";

        $html .= "<pre>" . "<b>Stack Trace:</b>\n";

        $count = 1;
        for ($i = 0; $i < count($this -> trace); $i++)
        {
            $instance = $this->trace[$i];

            if ($i == 0)
            {
                $prev_inst = [
                    'function' => 'GLOBAL',
                    'type' => '::',
                    'class' => 'GLOBAL'
                ];
            }
            else
            {
                $prev_inst = $this->trace[$i-1];
            }

            $html .= "<hr /><b>#{$count} - " . $instance['file'] . "</b>\n";
            $html .= "\tFunction: " . $prev_inst['class'] . $prev_inst['type'] . $prev_inst['function'] . "();\n";
            $html .= "\tLine: " . $instance['line'];

            $html .= "\n\tCalling to: <b>" . $instance['class'] . $instance['type'] . $instance['function'] . "();</b>\n";

            $count++;
        }

        return $html . "<hr />\n</pre>";
    }

}
