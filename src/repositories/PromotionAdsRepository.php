<?php

namespace App\repositories;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Image;
use PromotionAd;

class PromotionAdsRepository extends EntityRepository {
    function create(Image $image, bool $main ) {
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

}