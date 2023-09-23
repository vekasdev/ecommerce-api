<?php


namespace App\repositories;

use App\dtos\UploadedImage;
use Doctrine\ORM\EntityRepository;

class ImagesRepository extends EntityRepository {
    function addImage(UploadedImage $uploadedImage) {
        $image = new \Image();
        $image->setExtension($uploadedImage->fileExtention);
        $image->setFileName($uploadedImage->fileName);
        $this->getEntityManager()->persist($image);
    }
}