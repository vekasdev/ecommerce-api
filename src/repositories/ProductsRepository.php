<?php


namespace App\repositories;

use App\dtos\ProductData;
use App\dtos\ProductDataFiltering;
use App\exceptions\EntityNotExistException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Product;

class ProductsRepository extends EntityRepository {

    /** 
     * @return int id of deleted product 
     * @throws EntityNotExistException if the product not exist
     * */
    function delete(Product $product) : int {
        $id = $product->getId();
        $this->getEntityManager()->remove($product);
        $this->getEntityManager()->flush();
        return $id;
    }

    function addProduct(ProductData $data) : Product{
        $product = new Product;
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

        if (count($productData->images)>0){
            foreach($product->getImages() as $image){
                $this->getEntityManager()->remove($image);
            }
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
            $qb ->innerJoin("p.categories","c")
                ->andWhere($qb->expr()->eq("c.id",":categoryId"))
                ->setParameter("categoryId",$filtering->category);
        } else {
            $qb->leftJoin("p.categories","c");
        }

        if(isset($filtering->mainCategory)) {
            $qb ->innerJoin("c.parentCategories","pc")
                ->andWhere($qb->expr()->eq("pc.id",":mainCategoryId"))
                ->setParameter("mainCategoryId",$filtering->mainCategory);
        }

        if(isset($filtering->minProductDiscount)) {
            $qb->andWhere("p.discountPrecentage >= :discountPrecentage")
            ->setParameter("discountPrecentage",$filtering->minProductDiscount);
        }

        if(isset($filtering->color)) {
            $qb->andWhere($qb->expr()->eq("co.id",":colorId"))
                ->setParameter("colorId",$filtering->color);
        }

        $paginator = new Paginator($qb,true);

        $data = [];

        /** @var Product $product */
        foreach($paginator as $product) {
            array_push($data,$this->productToArray($product));
        }

        return [
            "records" => $data,
            "products-number" => $paginator->count() 
        ];

    }

    function getProduct($id){ 
        if(!$product = $this->find($id)) throw new EntityNotExistException("product with id  : $id not exist");
        return $this->productToArray($product);
    }

    /** @param  Product $product */
    function productToArray($product) : array {
        $data = [];
        $data["name"] = $product->getProductName();
        $data["id"] = $product->getId();
        $data["price"] = $product->getOriginalPrice();
        $data["description"] = $product->getDescription();
        $data["discount"] = $product->getDiscountPrecentage();
        $data["stock-quantity"] = $product->getStockQuantity();
        
        // assosiation
        $data["images"] = [];
        $data["colors"] = [];
        $data["categories"] = [];


        /** @var \Image $image */
        foreach($product->getImages() as $image) {
            array_push($data["images"], [
                "name" => $image->getFileName(),
                "extention" => $image->getExtension()
            ]);
        }

        /** @var \Color $color */
        foreach($product->getColors() as $color) {
            array_push($data["colors"], [
                "name" => $color->getColorName(),
                "hex-code" => $color->getColorHexCode(),
                "id" => $color->getId()
            ]);
        }

        /** @var \Category $category */
        foreach($product->getCategories() as $category) {
            array_push($data["categories"], [
                "name" => $category->getCategoryName(),
                "id" => $category->getId(),
            ]);
        }
        return $data;

    }
    
}



