<?php

namespace App\repositories;
use App\dtos\DeliveryDataDTO;
use DeliveryData;
use Doctrine\ORM\EntityRepository;
use OrderGroup;

class DeliveryDataRepository extends EntityRepository {
    function createDeliveryData(\User $user ) : DeliveryData {
        $deliveryData = new DeliveryData();
        return $deliveryData;
    }

    function updateDeliveryData(DeliveryData $deliveryData ,DeliveryDataDTO $deliveryDataDTO) {
        $deliveryData->setPhoneNumber($deliveryDataDTO->phoneNumber);
        $deliveryData->setLocation($deliveryDataDTO->location);
        $deliveryData->setPostalCode($deliveryDataDTO->postalCode);
        $deliveryData->setDeliveryRegion($deliveryDataDTO->region);
        $deliveryData->setName($deliveryDataDTO->name);
        $deliveryData->setDefaultData($deliveryDataDTO->defaultData);
        $deliveryData->setDelivery($deliveryDataDTO->delivery);

        if(isset($deliveryDataDTO->mapsLocation) )
            $deliveryData->setMapsLocation(json_encode($deliveryDataDTO->mapsLocation)); 
        
        $this->getEntityManager()->persist($deliveryData);
        $this->getEntityManager()->flush();

        return $deliveryData;
    }
}