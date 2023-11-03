<?php

namespace App\repositories;

use App\exceptions\EntityNotExistException;
use Category;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;


class CategoriesRepository extends EntityRepository {
    function addCategory(string $categoryName) : Category{
        $category = new Category();
        $category->setCategoryName($categoryName);
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
        return $category;
    }

    /**
     * @param int[] $categories
     */
     function getCategories(array $categories){ 
        $categories_  = [];
        foreach($categories as $id) {
            if (!$category=$this->find($id)) throw new EntityNotExistException("the category with id $id not exist");
            array_push($categories_,$category);
        }
        return $categories_;
    }

    function updateCategory($id , $name) : Category{
        if($id instanceof Category){
            $category = $id;
        } else if(is_numeric($id)){ 
            $category = $this->find($id);
            if(!$category) throw new EntityNotExistException("category not exist");
        }

        $category->setCategoryName($name);

        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
        return $category;
    }

    /**
     * @throws EntityNotExistException
     */
    function removeCategory(int $id) {
        $category = $this->find($id);
        if(!$category )throw new EntityNotExistException("category with id ".$id." not exist");
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();
        return $category;
    }

    function getAll() {
        $qb = $this->createQueryBuilder("c");
        $qb->select("c");

        return $qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }
}