<?php

class XyneoError extends Exception
{

    /**
     * @param string $message
     * @param integer $code
     * @param Exception $previous
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        echo "<pre>";
        echo $message;
        echo "</pre>";
        exit();
    }
}

?>