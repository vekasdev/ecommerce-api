<?php

namespace App\model;

use App\interfaces\RequestValidatorInterface;
use Psr\Container\ContainerInterface;

class ValidatorFactory{
    function __construct(private ContainerInterface $container){}
    function make(string $class) : RequestValidatorInterface{
        $class = $this->container->get($class);
        if ( ! $class instanceof RequestValidatorInterface) {
            throw new \RuntimeException("the validator class is not exist");
        }
        return $class;
    }
}