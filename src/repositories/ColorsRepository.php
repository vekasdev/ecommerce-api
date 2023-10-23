<?php

namespace App\repositories;
use App\exceptions\EntityNotExistException;
use Doctrine\ORM\EntityRepository;

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
}