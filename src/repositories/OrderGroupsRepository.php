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

        $qb->select("og,u,c,o,p,dd,dc,dg")
        ->leftJoin("og.user","u")
        ->leftJoin("og.cart","c")
        ->leftJoin("c.orders","o")
        ->leftJoin("o.product","p")
        ->leftJoin("og.deliveryData","dd")
        ->leftJoin("og.discountCode","dc")
        ->leftJoin("dd.deliveryRegion","dg")
        ->orderBy("og.id","DESC");

        if(isset($filter["status"])) 
            $qb->andWhere($qb->expr()->eq("og.status",":status"))
            ->setParameter("status", $filter["status"]);

        if(isset($filter["id"])) 
            $qb->andWhere($qb->expr()->eq("u.id",":userId"))
            ->setParameter("userId",(int) $filter["id"]);

        $results = $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        /** @var OrderGroup $result */
        foreach($results as &$result) {
            $result["total"] = $this->find($result["id"])->getTotal();
        }

        return $results;
    }

    function getOrderGroupDetails(int $id) {
        $qb = $this->createQueryBuilder("og");
        $query = $qb->select("og");

        $query->where($qb->expr()->eq("og.id","?1"))
            ->setParameter(1, $id);

        return $query->getQuery()->getArrayResult()[0];
    }
}