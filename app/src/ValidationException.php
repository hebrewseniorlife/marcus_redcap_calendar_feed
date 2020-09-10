<?php

use Exception;
use JsonSerializable;

/**
 * ValidationException - Custom exception to handle data validations
 */
class ValidationException extends Exception implements JsonSerializable 
{
    
    /**
     * __construct
     *
     * @param  mixed $message
     * @param  mixed $code
     * @param  mixed $previous
     * @return void
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * __toString
     *
     * @return string
     */
    public function __toString() : string {
        return $this->message;
    }
    
    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array {
        return [ "message" => $this->message];
    }
}