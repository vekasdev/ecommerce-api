<?php

namespace App\repositories;

use App\exceptions\EntityNotExistException;
use Category;
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
            $category = $this->find($id);
            if (!$category) throw new EntityNotExistException("the category with id $id not exist");
            array_push($categories_,$category);
        }
        return $categories_;
    }
}