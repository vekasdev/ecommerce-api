<?php


namespace App\repositories;

use App\dtos\UploadedImage;
use Doctrine\ORM\EntityRepository;
use Image;

class ImagesRepository extends EntityRepository {
    function addImage(UploadedImage $uploadedImage) : Image {
        $image = new Image();
        $image->setExtension($uploadedImage->fileExtention);
        $image->setFileName($uploadedImage->fileName);
        $this->getEntityManager()->persist($image);
        return $image;
    }

    function getImage($fileName) : Image {
        $qb = $this->createQueryBuilder("i");
        $query = $qb->select("i")
        ->where($qb->expr()->eq("i.fileName","?1"))
        ->setParameter(1,$fileName)
        ->getQuery();

        return $query->getSingleResult();
    }
}