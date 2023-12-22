<?php

namespace App\repositories;
use App\dtos\DeliveryDataDTO;
use DeliveryData;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use OrderGroup;
use Doctrine\ORM\NoResultException;
class DeliveryDataRepository extends EntityRepository {
    function createDeliveryData(\User $user ) : DeliveryData {
        $deliveryData = new DeliveryData();
        $deliveryData->setUser($user);
        $this->getEntityManager()->persist($deliveryData);
        $this->getEntityManager()->flush();
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

        if ($deliveryDataDTO->defaultData == true ) {

        }
        if(isset($deliveryDataDTO->mapsLocation) )
            $deliveryData->setMapsLocation(json_encode($deliveryDataDTO->mapsLocation)); 
        
        return $deliveryData;
    }

    /**
     * @throws NoResultException
     */
    function getDefaultDeliveryData($userId) {
        $qb = $this->createQueryBuilder("dd");

        $qb->select("dd,dg")
            ->leftJoin("dd.user","u")
            ->leftJoin("dd.deliveryRegion","dg")
            ->where($qb->expr()->eq("u.id","?1"))
            ->andWhere($qb->expr()->eq('dd.defaultData',"?2"))
            ->setMaxResults(1)
            ->setParameters([
                1=>$userId,
                2=>true
            ]);

        return $qb->getQuery()->getSingleResult(AbstractQuery::HYDRATE_ARRAY);
    }

    
}