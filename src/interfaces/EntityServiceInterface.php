<?php
namespace App\interfaces;

use Psr\Container\ContainerInterface;

interface EntityServiceInterface {
    function __construct(ContainerInterface $container, int $id);
    function remove() : void;
}