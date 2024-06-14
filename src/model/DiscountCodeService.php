<?php

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\interfaces\EntityServiceInterface;
use DiscountCode;
use Doctrine\ORM\EntityManager;
use Exception;
use Image;
use Psr\Container\ContainerInterface;

/**
 * @extends EntityService<DiscountCode>
 */
class DiscountCodeService extends EntityService implements EntityServiceInterface{
    protected $entityClassName = DiscountCode::class;

    function __construct(
        protected ContainerInterface $container,
        int $id  
    ){
        parent::__construct($id);
    }

    function getImageService() : ImageService | null{
        $imageId = $this->getEntity()->getImage() ? $this->getEntity()->getImage()->getId() : null ;
        if (!$imageId) return null;
        return $this->getEntityServiceProvider()->provide(Image::class,$this->getEntity()->getImage()->getId());
    }

    function remove() : void {
        $imageService = $this->getImageService();
        try {
            $this->getEntityManager()->beginTransaction();
            $this->getEntityManager()->remove($this->getEntity());
            $this->getEntityManager()->flush();
            if($imageService)$imageService->remove();
            $this->getEntityManager()->commit();
        } catch(Exception $e) {
            $this->getEntityManager()->rollback();
            throw new Exception(previous:$e);
        }
    }

}