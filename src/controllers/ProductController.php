<?php


namespace App\controllers;

use App\dtos\EntryPersisted;
use App\dtos\ProductDataFiltering;
use App\dtos\ResponseDataTransfer;
use App\dtos\UploadedImage;
use App\exceptions\RequestValidatorException;
use App\exceptions\UploadedFileException;
use App\repositories\ProductsRepository;
use Doctrine\ORM\EntityManager;
use responses\productsResponse;
use Slim\App;
use Vekas\ResponseManager\ResponseManager;
use App\dtos\ProductData;
use App\exceptions\EntityNotExistException;
use App\model\ImagesService;
use App\model\ValidatorFactory;
use App\repositories\CategoriesRepository;
use App\repositories\ColorsRepository;
use App\repositories\ImageRespository;
use App\repositories\ImagesRepository;
use App\validators\AddProductValidator;
use App\validators\GetProductsValidator;
use Category;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use responses\EntryPersistedResponse;
use responses\JsonResponse;
use Slim\Http\Interfaces\ResponseInterface;
use Slim\Http\ServerRequest;
use Slim\Http\Response;
use Slim\Psr7\UploadedFile;
use Symfony\Component\VarDumper\VarDumper;

class ProductController {
    private ProductsRepository $productsRepository;
    private ImagesRepository $imagesRepository;

    private CategoriesRepository $categoriesRepository;

    private ColorsRepository $colorsRepository;

    function __construct(
        private EntityManager $entityManager,
        private ResponseManager $responseManager,
        private ImagesService $imagesService,
        private ValidatorFactory $validatorFactory
    ){
        $this->productsRepository = $entityManager->getRepository(\Product::class);
        $this->imagesRepository = $entityManager->getRepository(\Image::class);
        $this->categoriesRepository = $entityManager->getRepository(Category::class);
        $this->colorsRepository = $entityManager->getRepository(\Color::class);
    }

    function getAllProducts(ServerRequest $req, Response $res){
        //"pageCountOnly', "name","minPrice","maxPrice","description","minStockQuantity","category","productDiscount","color","limit","fromIndex"
        $data = $req->getQueryParams();
        try {
            // validate request
            $validator = $this->validatorFactory->make(GetProductsValidator::class);
            $validator->validate($data);

            // data filtering
            $filtering = $this->createProductDataFiltering($data);

            // get and response
            $result = $this->productsRepository->getProducts($filtering);

            // register how many items returned header
            $res = $this->getIncomingItemsCountResponse($res,$result["pagesCount"]);

            $resData = new ResponseDataTransfer($res,200,$result["records"]);
        } catch(RequestValidatorException $e) {
            $resData = new ResponseDataTransfer($res,400,$e);
        }
        return $this->responseManager->getResponse(JsonResponse::class , $resData);
    }

    function addProduct(ServerRequest $req, Response $res){

        // name , price, description, stock-qty, categories, images, colors, discount-precentage
        
        $data = $req->getParsedBody();
        
        try {

            $this->validatorFactory->make(GetProductsValidator::class)->validate($data);

            // getting the related entities
            $categories = $this->categoriesRepository->getCategories(CSV2ARRAY($data["categories"]));
            $colors = $this->colorsRepository->getColors(CSV2ARRAY($data["colors"]));

            $discountPrecentage = $data["discount-precentage"];

            // upload images
            $images = $this->uploadImages($req->getUploadedFiles()["images"]);

            $data = new ProductData(
                $data["name"],
                $data["price"],
                $data["description"],
                $data["stock-qty"],
                $categories,
                $images,
                $discountPrecentage??$discountPrecentage,
                $colors
            );
    
            //unique constraint violation
            $product = $this->productsRepository->addProduct($data);
            
            $res = $res->withJson(
                [
                    "product-name" => $product->getProductName(),
                    "id" => $product->getId()
                ]
            );

        } catch (UploadedFileException $e) {
            $res = $res->withJson(["message" =>$e->getMessage()],400);
        } catch (EntityNotExistException $e) {
            $res = $res->withJson(["message" =>$e->getMessage()],400);
        } catch (UniqueConstraintViolationException $e) {
            $res = $res->withJson(["message" =>$e->getMessage()],400);
        } catch (RequestValidatorException $e) {
            $res = $res->withJson($e,400);
        }

        return $res;
    }


    // this is put method that need a whole fields to update the product
    function updateProduct(ServerRequest $req, Response $res,$args) {
        // product id in params
         // name , price, description, stock-qty, categories, images, colors, discount-precentage
        try {
            $data = $req->getParsedBody();
            $id = $args["id"];

            $this->validatorFactory->make(AddProductValidator::class)->validate($data);

            // getting the related entities
            $categories = $this->categoriesRepository->getCategories(CSV2ARRAY($data["categories"]));
            $colors = $this->colorsRepository->getColors(CSV2ARRAY($data["colors"]));

            $discountPrecentage = $data["discount-precentage"];

            // upload images
            $images = $this->uploadImages($req->getUploadedFiles()["images"]);



            $data = new ProductData(
                $data["name"],
                $data["price"],
                $data["description"],
                $data["stock-qty"],
                $categories,
                $images,
                $discountPrecentage??$discountPrecentage,
                $colors
            );
            
            $product =  $this->productsRepository->find($id);

            $oldImages = $this->getImagesFullFileName($product->getImages());

            $product = $this->productsRepository->updateProduct($id,$data);

            // remove old images from the storage
            foreach($oldImages as $image) {
                $this->imagesService->deleteImage($image);
            }

            $res = $res->withJson(
                [
                    "message" => "product updated successfully",
                    "status" => "success",
                    "product-name" => $product->getProductName(),
                    "id" => $product->getId()
                ]
            );

            // the duplication exception is on the many to many relationship
        } catch (UploadedFileException $e) {
            $res = $res->withJson(["message" =>$e->getMessage()],400);
        } catch (EntityNotExistException $e) {
            $res = $res->withJson(["message" =>$e->getMessage()],400);
        } catch (UniqueConstraintViolationException $e) {
            $res = $res->withJson(["message" =>"the product updated info has a duplicated match"],400);
        } catch (RequestValidatorException $e) {
            $res = $res->withJson($e,400);
        }

        return $res;
    }

    private function getImagesFullFileName($images) {
        $images_ = [] ;

        /**
         * @var \Image $image
         */
        foreach($images as $image) {
            array_push($images_,$image->getFullFileName());
        }

        return $images_;
    }


    /**
     * @return \Image[]
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

    private function createProductDataFiltering(array $data){
        $filters = new ProductDataFiltering();

        if(isset($data["name"])) $filters->name = $data["name"];
        if(isset($data["category"])) $filters->category = $data["category"];
        if(isset($data["description"])) $filters->description = $data["description"];
        if(isset($data["color"])) $filters->color = $data["color"];
        if(isset($data["limit"])) $filters->limit = $data["limit"];
        if(isset($data["minPrice"])) $filters->minPrice = $data["minPrice"];
        if(isset($data["maxPrice"])) $filters->maxPrice = $data["maxPrice"];
        if(isset($data["productDiscount"])) $filters->productDiscount = $data["productDiscount"];
        if(isset($data["fromIndex"])) $filters->fromIndex = $data["fromIndex"];
        if(isset($data["minStockQuantity"])) $filters->minStockQuantity = $data["minStockQuantity"];

        return $filters;
    }

    private function getIncomingItemsCountResponse(ResponseInterface $res,int $pageCount) {
        return  $res->withAddedHeader("response-pages-count",$pageCount);
    }

}