<?php

namespace App\model;

use App\exceptions\ServiceNotExistException;
use App\interfaces\EntityServiceProviderInterface;
use App\interfaces\EntityServiceInterface;
use Psr\Container\ContainerInterface;

class EntityServiceProvider implements EntityServiceProviderInterface{

    private $services = [];

    function __construct(private ContainerInterface $container){}


    /**
     * @throws ServiceNotExistException if provider not registered
     */
    function provide($class,$id) : EntityServiceInterface{
        $entityClasses = array_keys($this->services);
        if(($i = array_search($class,$entityClasses)) !== false) {
            return new ($this->services[$entityClasses[$i]])($this->container,$id);
        } 
        throw new ServiceNotExistException("entity provider not registered");
    }

    function registerService(string $entityClass,string $serviceClass ) {
        $this->services[$entityClass] = $serviceClass;
    }

}