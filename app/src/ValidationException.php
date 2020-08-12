<?php

use Exception;
use JsonSerializable;

class ValidationException extends Exception implements JsonSerializable 
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return $this->message;
    }

    public function jsonSerialize() {
        return [ "message" => $this->message];
    }
}