<?php

namespace App\repositories;

use App\exceptions\EntityNotExistException;
use Doctrine\ORM\EntityRepository;
use MainCategory;
use Category;
use Doctrine\ORM\AbstractQuery;

class MainCategoriesRepository extends EntityRepository {

    /** @var \Category[] $categories*/
    function create($name,$categories=[] ) {
        $mainCategory = new MainCategory();
        $mainCategory->setName($name);
        foreach ($categories as $cat) { 
            $mainCategory->addCategory($cat);
        }
        $this->getEntityManager()->persist($mainCategory);
        $this->getEntityManager()->flush();
        return $mainCategory;
    }

    function delete($mainCat) {
        if(is_int($mainCat)) {
            $_id = $mainCat;
            if(!$mainCat = $this->find($mainCat)) {
                throw new EntityNotExistException("main-category with id : ".$_id." not exist" );
            }
        } else if ($mainCat instanceof MainCategory) {
        } else {throw new \InvalidArgumentException("\$mainCat value must be of type ".MainCategory::class." or int; ".gettype($mainCat) ." given");}
        
        $this->getEntityManager()->remove($mainCat);
        $this->getEntityManager()->flush();
        return $_id;
    }
    function getAll() {
        $qb = $this->createQueryBuilder("mc");
        $qb->select("mc,c")
        ->leftJoin("mc.categories","c");

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /** @var Category[] $categories */
    function update($mainCat,string  $name,$categories) { 
        if(is_int($mainCat)) {
            $_id = $mainCat;
            if(!$mainCat = $this->find($mainCat)) {
                throw new EntityNotExistException("main-category with id : ".$_id." not exist" );
            }
        } else if ($mainCat instanceof MainCategory) {
        } else {throw new \InvalidArgumentException("\$mainCat value must be of type ".MainCategory::class." or int; ".gettype($mainCat) ." given");}

        $mainCat->setName($name);
        $mainCat->clearCategories();

        foreach ($categories as $cat) {
            $mainCat->addCategory($cat);
        }

        $this->getEntityManager()->persist($mainCat);
        $this->getEntityManager()->flush();
        return $mainCat;
    }
}