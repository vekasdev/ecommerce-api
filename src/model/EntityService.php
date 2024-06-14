<?php


namespace App\model;

use App\exceptions\EntityNotRegisteredException;
use App\interfaces\EntityServiceInterface;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
use App\exceptions\EntityNotExistException;
use App\exceptions\InvalidConfigurationException;
use Psr\Container\NotFoundExceptionInterface;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @template T
 */
abstract class EntityService  {
    protected $entityClassName = "";
    
    /**
     * @var T
     */
    private $entity;
    
    protected ContainerInterface $container;
    function __construct(int $id) {
        $this->registerEntity($id);
    }

    /**
     * @throws EntityNotExistException when enity not exist
     * @throws InvalidConfigurationException when the entity class name not overrided in the extended class
     */
    protected function registerEntity($id) {
        try {
            $entity= $this->getEntityManager()->find($this->entityClassName,$id);
            if(!$entity) throw new EntityNotExistException("entry not exist");
            $this->entity = $entity;
        } catch (ORMInvalidArgumentException $e) {
            throw new InvalidConfigurationException(
                "it is necessary to override the protected \$entityClassName in the extended class");
        }
    }

    /**
     * @throws NotFoundExceptionInterface when EnityManager not exist
     */
    protected function getEntityManager() : EntityManager{
        return $this->container->get(EntityManager::class);
    }

    /**
     * @return T 
     * @throws EntityNotRegisteredException
     */
    function getEntity() {
        if(!$this->entity) throw new EntityNotRegisteredException;
        return $this->entity;
    }

    protected function remove() : void{
        $this->getEntityManager()->remove($this->entity);
        $this->getEntityManager()->flush();
    }

    function getEntityServiceProvider() : EntityServiceProvider{
        return $this->container->get(EntityServiceProvider::class);
    }
}