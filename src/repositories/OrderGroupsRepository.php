<?php

namespace App\repositories;
use Cart;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use OrderGroup;

class OrderGroupsRepository extends EntityRepository {
    function createOrderGroup() {
        $orderGroup = new OrderGroup();
        return $orderGroup;
    }

    function getOrderGroups($filter) {

        $qb = $this->createQueryBuilder("og");

        $qb->select("og")
        ->leftJoin("og.user","u")
        ->leftJoin("og.cart","c")
        ->orderBy("og.id","DESC");

        if(isset($filter["status"])) 
            $qb->andWhere($qb->expr()->eq("og.status",":status"))
            ->setParameter("status", $filter["status"]);

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
}