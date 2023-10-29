<?php


namespace App\repositories;

use App\dtos\ProductData;
use App\dtos\ProductDataFiltering;
use App\exceptions\EntityNotExistException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Product;

class ProductsRepository extends EntityRepository {
    function addProduct(ProductData $data) : \Product{
        $product = new \Product;
        $product->setDescription($data->description);
        $product->setPrice($data->price);
        $product->setProductName($data->name);
        $product->setStockQuantity($data->stockQuantity);

        foreach($data->images as $image){
            $product->addImage($image);
        }

        foreach($data->categories as $category) {
            $product->addCategory($category);
        }


        foreach($data->colors as $color) {
            $product->addColor($color);
        }

        $product->setDiscountPrecentage($data->productDiscount);

        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return $product;
    }

    function updateProduct(int $id,ProductData $productData) : Product {
        /**
         * @var Product
         */
        $product = $this->find($id);
        if(!$product) throw new EntityNotExistException("product with id : ".$id."not exist");

        foreach($product->getImages() as $image){
            $this->getEntityManager()->remove($image);
        }

        $product->clearCategorys();
        $product->clearColors();

        foreach($productData->images as $image){
            $product->addImage($image);
        }

        foreach($productData->categories as $category) {
            $product->addCategory($category);
        }

        foreach($productData->colors as $color) {
            $product->addColor($color);
        }

        $product->setDescription($productData->description);
        $product->setPrice($productData->price);
        $product->setProductName($productData->name);
        $product->setStockQuantity($productData->stockQuantity);
        $product->setDiscountPrecentage($productData->productDiscount);

        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return $product;
    }

    function getProducts(ProductDataFiltering $filtering) {
        $qb = $this->createQueryBuilder("p");
      
        $statement = $qb->select("p","c","co","img")
            ->setFirstResult($filtering->fromIndex)
            ->setMaxResults($filtering->limit)
            ->orderBy("p.id")
            ->leftJoin("p.categories","c")
            ->leftJoin("p.colors","co")
            ->leftJoin("p.images","img");

        if(isset($filtering->name)) {
            $qb->andWhere($qb->expr()->like("p.product_name",":name"))
                ->setParameter("name","%".$filtering->name."%");
        }

        if(isset($filtering->minPrice)) {
            $qb->andWhere($qb->expr()->gte("p.price",":minPrice"))
                ->setParameter("minPrice",$filtering->minPrice);
        }
        
        if(isset($filtering->maxPrice)) {
            $qb->andWhere($qb->expr()->gte("p.price",":maxPrice"))
                ->setParameter("maxPrice",$filtering->maxPrice);
        }

        if(isset($filtering->description)) {
            $qb->andWhere($qb->expr()->like("p.description",":description"))
                ->setParameter("description","%".$filtering->description."%");
        }
        
        if(isset($filtering->minStockQuantity)) {
            $qb->andWhere($qb->expr()->gte("p.stock_quantity",":minStockQuantity"))
                ->setParameter("minStockQuantity",$filtering->minStockQuantity);
        }

        if(isset($filtering->category)) {
            $qb->andWhere($qb->expr()->eq("c.id",":categoryId"))
                ->setParameter("categoryId",$filtering->category);
        }

        if(isset($filtering->productDiscount)) {
            $qb->andWhere($qb->expr()->lt("p.discountPrecentage",":discountPrecentage"))
            ->setParameter("discountPrecentage",$filtering->productDiscount);
        }

        if(isset($filtering->color)) {
            $qb->andWhere($qb->expr()->eq("co.id",":colorId"))
                ->setParameter("colorId",$filtering->color);
        }

        $paginator = new Paginator($qb);

        return [
            "records" => $statement->getQuery()->getArrayResult(),
            "pagesCount" => $paginator->count() / $filtering->limit
        ];
    }
    
}



