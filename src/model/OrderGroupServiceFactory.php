<?php

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\interfaces\NotificationSender;
use Doctrine\ORM\EntityManager;
use OrderGroup;
use Symfony\Component\DependencyInjection\Exception\InvalidParameterTypeException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class OrderGroupServiceFactory { 
    function __construct(
        private EntityManager $entityManager,
        private NotificationSender $notificationSender,
        private CartServiceFactory $cartServiceFactory
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
            $this->notificationSender ,$this->cartServiceFactory );
        } else if (is_int($orderGroup)) {
            $orderGroup_ = $this->entityManager->find(OrderGroup::class, $orderGroup);
            if( ! $orderGroup_ ) throw new EntityNotExistException( "orderGroup with id : ".$orderGroup." not exist" ) ;
            return new OrderGroupService($orderGroup_,$this->entityManager,
            $this->notificationSender ,$this->cartServiceFactory );
        } else {
            throw new InvalidParameterException();
        }
    }
}