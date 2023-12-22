<?php

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\interfaces\NotificationSender;
use DI\Container;
use Doctrine\ORM\EntityManager;
use OrderGroup;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidParameterTypeException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class OrderGroupServiceFactory { 
    function __construct(
        private EntityManager $entityManager,
        private NotificationSender $notificationSender,
        private CartServiceFactory $cartServiceFactory,
        private ContainerInterface $container
    ){

    }

    /**
     * @param OrderGroup | int $orderGroup
     * @throws InvalidParameterException
     * @throws EntityNotExistException
     */
    function make($orderGroup) : OrderGroupService {
        if($orderGroup instanceof OrderGroup) {
            return new OrderGroupService($orderGroup,$this->entityManager,
            $this->notificationSender ,$this->cartServiceFactory ,$this->container);
        } else if (is_int($orderGroup)) {
            $orderGroup_ = $this->entityManager->find(OrderGroup::class, $orderGroup);
            if( ! $orderGroup_ ) throw new EntityNotExistException( "orderGroup with id : ".$orderGroup." not exist" ) ;
            return new OrderGroupService($orderGroup_,$this->entityManager,
            $this->notificationSender ,$this->cartServiceFactory,$this->container );
        } else {
            throw new InvalidParameterException();
        }
    }
}