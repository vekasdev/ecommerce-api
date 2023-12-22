<?php

namespace App\repositories;

use App\exceptions\DuplicatedEntryException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Exception;
use Image;
use PromotionAd;

class PromotionAdsRepository extends EntityRepository {
    function create(Image $image, bool $main ) {
        if($main == true ) {
            if($this->isMainExist()) {
                // here throw exception of duplicated entry - entity
                throw new DuplicatedEntryException("must one entry should be main");
            }
        }
        $promotionAd = new PromotionAd();
        $promotionAd->addImage($image);
        $promotionAd->setMain($main);

        $this->getEntityManager()->persist($promotionAd); 
        $this->getEntityManager()->flush();
        
        return $promotionAd;
    }

    function getAll() { 
        $qb = $this->createQueryBuilder("pa");
        return $qb->select("pa,im")
              ->leftJoin("pa.images","im")
              ->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    function isMainExist() {
        $qb = $this->createQueryBuilder("pa");
        try {
            $promotionAd = $qb->select("pa")
            ->where("pa.main",$qb->expr()->eq("pa.main","?1"))
            ->setParameter(1,true)
            ->getQuery()
            ->getSingleResult();
        } catch (NoResultException $e) {
            return true;
        } catch (Exception $e) {
            return true;
        }
        return false;
    }

}