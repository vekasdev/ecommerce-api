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
use App\validators\GetProductsValidator;
use Category;
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
            
            // getting the related entities
            $categories = $this->categoriesRepository->getCategories(json_decode($data["categories"]));
            $colors = isset($data["colors"]) ? $this->colorsRepository->getColors(json_decode($data["colors"]))  : null ;
            $images = $this->uploadImages($req->getUploadedFiles()["images"]);
            $discountPrecentage = $data["discount-precentage"] ?? $data["discount-precentage"] ;


            $data = new ProductData(
                $data["name"],
                $data["price"],
                $data["description"],
                $data["stock-qty"],
                $categories,
                $images,
                $discountPrecentage??$discountPrecentage,
                $colors ?? $colors
            );
    

            //unique constraint violation
            $product = $this->productsRepository->addProduct($data);
            
            $dataRes = new ResponseDataTransfer($res,200,[
                "pname" => $product->getProductName(),
                "pid" => $product->getId()
            ]);

            
        } catch (UploadedFileException $e) {
            $dataRes = new ResponseDataTransfer($res,400,[
                "message" =>$e->getMessage()
            ]);
        } catch (EntityNotExistException $e) {
            $dataRes = new ResponseDataTransfer($res,400,[
                "message" =>$e->getMessage()
            ]);
        } catch (UniqueConstraintViolationException $e) {
            $dataRes = new ResponseDataTransfer($res,400,[
                "message" => "product is already exist ! try other details"
            ]);
        }


        $response = $this->responseManager->getResponse(JsonResponse::class,$dataRes);
        return $response;
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