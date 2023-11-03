<?php


namespace App\model;

use App\repositories\ProductsRepository;
use Doctrine\ORM\EntityManager;
use Product;

class ProductService {
    private ProductsRepository $productsRepository;
    function __construct(
        private Product $product,
        private EntityManager $em,
        private ImagesService $imagesService
    )  {
        $this->productsRepository = $em->getRepository(Product::class);
    }

    function delete() {
        /** @var \Image $image */
        foreach($this->product->getImages() as $image) {
            $this->imagesService->deleteImage($image->getFullFileName());
        }
        $this->productsRepository->delete($this->product);
        return true;
    }

}