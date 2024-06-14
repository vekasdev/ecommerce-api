<?php 

namespace App\repositories;

use App\exceptions\EntityNotExistException;
use Closure;
use DiscountCode;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Image;

class DiscountCodeRepository extends EntityRepository {
    function getDiscountCode($code) : DiscountCode | null{
        $qb = $this->createQueryBuilder("d");
        $qb->where($qb->expr()->eq("d.code","?1"))
        ->setParameter(1, $code);

        return $qb->getQuery()->getOneOrNullResult();
    }

    function deleteDiscountCode(int $id){
        if(!$dc = $this->find($id)) {
            throw new EntityNotExistException("discount-code with id $id not exist");
        }
        $this->getEntityManager()->remove($dc);
        $this->getEntityManager()->flush();
        return true;
    }

    function getDiscountCodeById($id) {
        $qb = $this->createQueryBuilder("dc");

        $qb->select("dc,i")
        ->leftJoin("dc.image","i")
        ->where("dc.id = :id")
        ->setParameter("id",$id);

        $result =  $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
        if(count($result) > 0 ) {
            return $result[0];
        }
        return null;
    }

    function createDiscountCode(string $code, int $precentage, bool $valid,bool $promoted,?Image $image ){ 
        $dcode = new DiscountCode();
        $dcode->setCode($code);
        $dcode->setPrecentage($precentage*pow(10, -2));
        $dcode->setValid($valid);
        $dcode->setPromoted($promoted);
        if($image) 
            $dcode->setImage($image);
        $this->getEntityManager()->persist($dcode);
        $this->getEntityManager()->flush();
        return $dcode;
    }

    function updateDiscountCode($discountCode ,$code, int $precentage, bool $valid,bool $promoted,Image | null $image=null) {
        if(! $discountCode instanceof DiscountCode){
            $discountCode = $this->find($discountCode);
            if(!$discountCode) throw new EntityNotExistException("discount code not exist") ;
        }

        if($image) $discountCode->setImage($image);
        
        $discountCode->setCode($code);
        $discountCode->setPrecentage($precentage*pow(10, -2));
        $discountCode->setValid($valid);
        $discountCode->setPromoted($promoted);
        $this->getEntityManager()->persist($discountCode);
        $this->getEntityManager()->flush();
        return $discountCode;
    }

    function getDiscountCodes() {
        $qb = $this->createQueryBuilder("dc");
        $qb->select("dc","i")
        ->leftJoin("dc.image","i");

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }


    
}