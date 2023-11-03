<?php 

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\repositories\ProductsRepository;
use Doctrine\ORM\EntityManager;
use Product;

class ProductServiceProvider { 
    private ProductsRepository $productsRepository;
    function __construct(
        private EntityManager $entityManager,
        private ImagesService $imagesService
    ) {
        $this->productsRepository = $this->entityManager->getRepository(Product::class);
    }

    function make($product) {
        if($product instanceof Product) {
            $_product = $product;
        } else if (is_int($product)) { 
            if(!$_product =$this->productsRepository->find($product)) { 
                throw new EntityNotExistException("product with id : ".$product.",not exist");
            }
        } else { throw new \InvalidArgumentException("product parameter must be of type int,Product;".gettype($product)." given");}

        $productService = new ProductService($_product,$this->entityManager,$this->imagesService);
        
        return $productService;
    }
}