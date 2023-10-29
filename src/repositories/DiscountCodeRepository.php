<?php 

namespace App\repositories;

use App\exceptions\EntityNotExistException;
use DiscountCode;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

class DiscountCodeRepository extends EntityRepository {
    function getDiscountCode($code){
        $qb = $this->createQueryBuilder("d");
        $qb->where($qb->expr()->eq("d.code","?1"))
        ->setParameter(1, $code);

        return $qb->getQuery()->getOneOrNullResult();
    }

    function createDiscountCode(string $code, int $precentage, bool $valid){ 
        $dcode = new DiscountCode();
        $dcode->setCode($code);
        $dcode->setPrecentage($precentage*pow(10, -2));
        $dcode->setValid($valid);
        $this->getEntityManager()->persist($dcode);
        $this->getEntityManager()->flush();
        return $dcode;
    }

    function updateDiscountCode($discountCode ,$code, int $precentage, bool $valid) {
        if(! $discountCode instanceof DiscountCode){
            $discountCode = $this->find($discountCode);
            if(!$discountCode) throw new EntityNotExistException("discount code not exist") ;
        }

        $discountCode->setCode($code);
        $discountCode->setPrecentage($precentage*pow(10, -2));
        $discountCode->setValid($valid);
        $this->getEntityManager()->persist($discountCode);
        $this->getEntityManager()->flush();
        return $discountCode;
    }

    function getDiscountCodes() {
        $qb = $this->createQueryBuilder("dc");
        $qb->select("dc");

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
    
}