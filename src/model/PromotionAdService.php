<?php

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\repositories\ImagesRepository;
use App\repositories\PromotionAdsRepository;
use Doctrine\ORM\EntityManager;
use Image;
use PromotionAd;

class PromotionAdService {
    private PromotionAdsRepository $promotionAdsRepository;

    private ImagesRepository $imageRepository;
    function __construct(
        private PromotionAd $promotionAd,
        private EntityManager $em,
        private ImagesService $imagesService
    ) {
        $this->promotionAdsRepository = $em->getRepository(PromotionAd::class);
        $this->imageRepository = $em->getRepository(Image::class);
    }

    function update(Image $image,bool $main ) {
        $this->checkExistance();
        $this->clearImages();
        $this->promotionAd->addImage($image);
        $this->promotionAd->setMain($main);
        $this->save();
    }

    function remove() {
        $this->checkExistance();

        foreach($this->promotionAd->getImages() as $image) {
            $this->imagesService->deleteImage($image->getFullFileName());
        }

        $this->em->remove($this->promotionAd);
        $this->em->flush();
        
        return true;
    }

    function save(){
        $this->em->persist($this->promotionAd);
        $this->em->flush();
    }

    private function checkExistance() {
        if($this->promotionAd->getId() == null) {
            throw new EntityNotExistException("the promotion ad is not really exist to perform any operation");
        }
        return true;
    }

    function getEntity() {
        return $this->promotionAd;
    }

    private function clearImages() {
        $image = $this->promotionAd->getImages();
        foreach($image as $image) {
            $this->imagesService->deleteImage($image->getFullFileName());
            $this->em->remove($image);
        }
        $this->em->flush();
    }

}