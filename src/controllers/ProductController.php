<?php


namespace App\controllers;

use App\dtos\EntryPersisted;
use App\dtos\UploadedImage;
use App\exceptions\UploadedFileException;
use App\repositories\ProductsRepository;
use Doctrine\ORM\EntityManager;
use responses\productsResponse;
use Slim\App;
use Vekas\ResponseManager\ResponseManager;
use App\dtos\ProductData;
use App\exceptions\EntityNotExistException;
use App\model\ImagesService;
use App\repositories\ImageRespository;
use App\repositories\ImagesRepository;
use Category;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use responses\EntryPersistedResponse;
use Slim\Http\ServerRequest;
use Slim\Http\Response;
use GuzzleHttp\Psr7\UploadedFile;

class ProductController {
    private ProductsRepository $productsRepository;
    private ImagesRepository $imagesRepository;
    function __construct(
        private EntityManager $entityManager,
        private ResponseManager $responseManager,
        private ImagesService $imagesService
    ){
        $this->productsRepository = $entityManager->getRepository(\Product::class);
        $this->imagesRepository = $entityManager->getRepository(\Image::class);
    }

    function getAllProducts(ServerRequest $req, Response $res){
        return $this->responseManager->getResponse(productsResponse::class,[]);
    }

    function addProduct(ServerRequest $req, Response $res){
        $data = $req->getParsedBody();
        
        // get related entities
        try {
            $categories = $this->getCategories(json_decode($data["categories"]));
            $images = $this->uploadImages($req->getUploadedFiles());
        } catch (UploadedFileException $e) {
            return $this->responseManager->getResponse(
                EntryPersistedResponse::class,
                new EntryPersisted($res,false,["message"=>$e->getMessage()])
            );
        } catch (EntityNotExistException $e) {
            return $this->responseManager->getResponse(
                EntryPersistedResponse::class,
                new EntryPersisted($res,false,["message"=>$e->getMessage()])
            );
        }

        $data = new ProductData(
            $data["name"],
            $data["price"],
            $data["description"],
            $data["stock-qty"],
            $categories,
            $images
        );

        try {
            $product = $this->productsRepository->addProduct($data);
        } catch (UniqueConstraintViolationException $e) {
            return $this->responseManager->getResponse(
                EntryPersistedResponse::class,
                new EntryPersisted($res,false,["message"=>"product is already existed"])
            );
        }
        
        $data = new EntryPersisted($res,true,[
            "pname" => $product->getProductName(),
            "pid" => $product->getId()
        ]);
        $response = $this->responseManager->getResponse(EntryPersistedResponse::class,$data);
        return $response;
    }


    /**
     * @return array<\Image>
     */
    function uploadImages(array $uploadedFiles){
        $images = [];
        /**
         * @var UploadedFile $file
         */
        foreach($uploadedFiles as $file){
            $image = $this->imagesService->save($file);
            $image = $this->imagesRepository->addImage($image);
            array_push($images,$image);
        }
        return $images;
    }

    /**
     * @var int[] $categories 
     * @return array<Category>
     */
    function getCategories(array $categories){
        $categories_  = [];
        foreach($categories as $id) {
            $category = $this->entityManager->find(Category::class,$id);
            if (!$category) throw new EntityNotExistException("the category with id $id not exist");
            array_push($categories_,$category);
        }
        return $categories_;
    }
}