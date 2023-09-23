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
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return $product;
    }
}