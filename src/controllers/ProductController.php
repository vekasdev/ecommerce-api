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
use App\model\ImagesService;
use responses\EntryPersistedResponse;
use Slim\Http\ServerRequest;
use Slim\Http\Response;
use GuzzleHttp\Psr7\UploadedFile;
class ProductController {
    private ProductsRepository $productsRepository;
    function __construct(
        private EntityManager $entityManager,
        private ResponseManager $responseManager,
        private ImagesService $imagesService
    ){
        $this->productsRepository = $entityManager->getRepository(\Product::class);
    }

    function getAllProducts(ServerRequest $req, Response $res){
        return $this->responseManager->getResponse(productsResponse::class,[]);
    }

    function addProduct(ServerRequest $req, Response $res){
        $images = [];
        $categories = [];

        /**
         * @var UploadedFile $file
         */
        foreach($req->getUploadedFiles() as $file){
            try {
                $image = $this->imagesService->save($file);
                array_push($images,)
            }catch(UploadedFileException $e) {
                
            }
        }
        $data = $req->getParsedBody();
        $data = new ProductData(
            $data["name"],
            $data["price"],
            $data["description"],
            $data["stock-qty"],
            new UploadedImage("fsdfsodfms","image/jpeg"),
            $categories,
            $images
        );
        $product = $this->productsRepository->addProduct($data);
        $data = new EntryPersisted(true,[
            "pname" => $product->getProductName(),
            "pid" => $product->getId()
        ]);
        $response = $this->responseManager->getResponse(EntryPersistedResponse::class,$data);
        return $response;
    }
}