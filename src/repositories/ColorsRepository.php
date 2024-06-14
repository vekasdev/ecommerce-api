<?php

namespace App\repositories;
use App\exceptions\EntityNotExistException;
use Color;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;

class ColorsRepository extends EntityRepository {

    /**
     * @param int[] $colors
     */
    function getColors(array $colors){ 
        $colors_  = [];
        foreach($colors as $id) {
            $color = $this->find($id);
            if (!$color) throw new EntityNotExistException("the color with id $id not exist");
            array_push($colors_,$color);
        }
        return $colors_;
    }

    function getAll() {
        $qb = $this->createQueryBuilder("co");
        $qb->select("co");

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }


    /**
     * @throws UniqueConstraintViolationException
     */
    function createColor($name,$hexCode) : Color {
        $color = new Color;
        $color->setColorName($name);
        $color->setColorHexCode($hexCode);
        $this->getEntityManager()->persist($color);
        $this->getEntityManager()->flush();
        return $color;
    }

    function updateColor(int $id,$name,$hexCode) {
        $color = $this->find($id);
        if(!$color) throw new EntityNotExistException("color with id $id not exist");
        $color->setColorName($name);
        $color->setColorHexCode($hexCode);
        $this->getEntityManager()->persist($color);
        $this->getEntityManager()->flush();
        return $color;
    }

    function deleteColorById(int $id) {
        if(!$color = $this->find($id)) {
            throw new EntityNotExistException("color entry with id : $id not exist");
        }
        try {
            $this->getEntityManager()->beginTransaction();
            $this->getEntityManager()->remove($color);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->commit();
        } catch (Exception $e) {
            $this->getEntityManager()->rollback();
            return false;
        }
        return true;
    }
    
}