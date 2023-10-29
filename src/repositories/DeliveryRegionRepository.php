<?php

namespace App\repositories;

use App\dtos\DeliveryRegionDataDTO;
use App\exceptions\EntityNotExistException;
use DeliveryRegion;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

class DeliveryRegionRepository extends EntityRepository {
    function getAll(null | DeliveryRegionDataDTO $filter = null) {
        $qb = $this->createQueryBuilder("dr");
        $qb->select("dr");
        if($filter !== null) {
            if($filter->region !== null ) {
                $qb->andWhere($qb->expr()->eq("dr.name",":name"))->set("name", $filter->region);
            }
            if($filter->cost != null) {
                $qb->andWhere($qb->expr()->gt("dr.deliveryCost",":cost"))->setParameter("cost", $filter->cost);
            }
            if($filter->available !== null) {
                $qb->andWhere($qb->expr()->eq("dr.available",":available"))->setParameter("available", $filter->available);
            }
        };   
        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY); 
        }


    function save($name,$cost,bool $available) {
        $dg = new DeliveryRegion();
        $dg->setName($name);
        $dg->setDeliveryCost($cost);
        $dg->setAvailable($available);
        $this->getEntityManager()->persist($dg);
        $this->getEntityManager()->flush();
        return $dg;
    }

    function update($id,string $name,float $cost,bool $available) {
        if($id instanceof DeliveryRegion) {
            $dg = $id;
        } else if (is_numeric($id)) {  
            $dg = $this->find($id);
            if($dg === null) throw new EntityNotExistException();
        } 

        $dg->setName($name)
        ->setAvailable($available)
        ->setDeliveryCost($cost);

        $this->getEntityManager()->persist($dg);
        $this->getEntityManager()->flush();
        return $dg;
    }
    
}