<?php 

namespace App\repositories;

use Doctrine\ORM\EntityRepository;

class DiscountCodeRepository extends EntityRepository {
    function getDiscountCode($code){
        $qb = $this->createQueryBuilder("d");
        $qb->where($qb->expr()->eq("d.code","?1"))
        ->setParameter(1, $code);

        return $qb->getQuery()->getOneOrNullResult();
    }
}