<?php

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\repositories\PromotionAdsRepository;
use Doctrine\ORM\EntityManager;
use PromotionAd;

class PromotionAdServiceProvider {

    private PromotionAdsRepository $promotionAdsRepository;
    function __construct(
        private EntityManager $entityManager,
        private ImagesService $imageService
    ){
        $this->promotionAdsRepository = $entityManager->getRepository(PromotionAd::class);
    }
    function make($promotionAd) {
        if($promotionAd instanceof PromotionAd) {
            $_promotionAd = $promotionAd;
        } else if (is_int($promotionAd)) {
            if(!$_promotionAd = $this->promotionAdsRepository->find($promotionAd)) {
                throw new EntityNotExistException("promotion ad with id : $promotionAd not exist");
            }
        } else {
            throw new \InvalidArgumentException("promotion ad parameter must be of type PromotionAd or integer; "
                .gettype($promotionAd)." given");
        }

        return new PromotionAdService($_promotionAd,$this->entityManager,$this->imageService);
    }
}