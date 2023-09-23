<?php

namespace App\exceptions;

class RequestValidatorException extends \Exception implements \JsonSerializable{
    function __construct(private array $errors ,  $message = "",  $code = 0,  $previous = null) {

    }

    function jsonSerialize(): mixed {
        return $this->errors;
    }
}