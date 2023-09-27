<?php


namespace App\repositories;

use App\dtos\ProductData;
use Doctrine\ORM\EntityRepository;

class ProductsRepository extends EntityRepository {
    function addProduct(ProductData $data) : \Product{
        $product = new \Product;
        $product->setDescription($data->description);
        $product->setPrice($data->price);
        $product->setProductName($data->name);
        $product->setStockQuantity($data->stockQuantity);
        /**
         * @var \Image $image
         */
        foreach($data->images as $image){
            $product->addImage($image);
        }

        /**
         * @var \Category $category
         */
        foreach($data->categories as $category) {
            $product->addCategory($category);
        }

        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return $product;
    }
}