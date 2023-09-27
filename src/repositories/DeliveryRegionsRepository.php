<?php


namespace App\repositories;
use Doctrine\ORM\EntityRepository;


class DeliveryRegionsRepository extends EntityRepository {
    function addRegion(string $name , float $cost) {
        $region = new \DeliveryRegion;
        $region->setName($name)
        ->setDeliveryCost($cost);
        $this->getEntityManager()->persist($region);
        $this->getEntityManager()->flush();
        return $region;
    }
}