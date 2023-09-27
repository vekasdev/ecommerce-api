<?php

namespace App\repositories;

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
}