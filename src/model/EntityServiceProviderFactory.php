<?php


namespace App\model;

use Psr\Container\ContainerInterface;

class EntityServiceProviderFactory {
    function __construct(private ContainerInterface $container) {}
    
    function registerFromArray($arr) {
        $entityServiceProvider = new EntityServiceProvider($this->container);
        foreach($arr as $entityClass => $serviceClass) {
            $entityServiceProvider->registerService($entityClass , $serviceClass);
        }
        return $entityServiceProvider;
    }
}