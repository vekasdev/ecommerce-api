<?php

namespace App\model;
use App\interfaces\EntityServiceInterface;
use Exception;
use Psr\Container\ContainerInterface;
use Image;

/**
 * @template T of Image
 */
class ImageService extends EntityService implements EntityServiceInterface{
    protected $entityClassName = Image::class;

    function __construct(protected ContainerInterface $container, int $id) {
        parent::__construct($id);
    }
    function remove() : void {
        $imagesService = $this->getImageService();
        
        /** @var Image */
        $entity = $this->getEntity();

        $oldFileName = $entity->getFullFileName();

        try {
            $this->getEntityManager()->beginTransaction();
            $this->getEntityManager()->remove($this->getEntity());
            $this->getEntityManager()->flush();
            $imagesService->deleteImage($oldFileName);
            $this->getEntityManager()->commit();
        } catch (Exception $e) {
            $this->getEntityManager()->rollback();
            throw new Exception(previous:$e);
        }

    }


    function getImageService() : ImagesService{
        return  $this->container->get(ImagesService::class);
    }
}

