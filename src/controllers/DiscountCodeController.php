<?php

namespace App\controllers;
use App\dtos\DeliveryDataDTO;
use App\dtos\ResponseDataTransfer;
use App\exceptions\ElementAlreadyExistsException;
use App\exceptions\EntityNotExistException;
use App\exceptions\OrderingProcessException;
use App\exceptions\ProcessedRequestException;
use App\exceptions\RequestValidatorException;
use App\exceptions\UploadedFileException;
use App\model\ImagesService;
use App\model\OrderGroupService;
use App\model\OrderGroupServiceFactory;
use App\model\ValidatorFactory;
use App\repositories\CartsRepository;
use App\repositories\DiscountCodeRepository;
use App\repositories\OrdersRepository;
use App\validators\AddOrderValidator;
use Cart;
use DiscountCode;
use Doctrine\ORM\EntityManager;
use OrderGroup;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Vekas\ResponseManager\ResponseManager;
use Order;
use responses\JsonResponse;
use App\model\UserService;
use App\repositories\DeliveryRegionRepository;
use App\repositories\ImagesRepository;
use App\repositories\OrderGroupsRepository;
use App\validators\CreateDiscountCodeValidator;
use App\validators\GetOrderGroupsValidator;
use App\validators\SetDeliveryDataValidator;
use DeliveryRegion;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Image;
use Slim\Psr7\UploadedFile;

class DiscountCodeController {
    private DiscountCodeRepository $discountCodeRepository;
    private ImagesRepository $imagesRepository;
    function __construct(
        private EntityManager $em,
        private ValidatorFactory $validatorFactory,
        private ImagesService $imagesService 
    
    ) {
        $this->discountCodeRepository = $em->getRepository(DiscountCode::class);
        $this->imagesRepository = $em->getRepository(Image::class);

    }

    function createDiscountCode(ServerRequest $req, Response $res,$args) {
        $id = $args["id"];
        $data = $req->getParsedBody();

        try {

            $this->validatorFactory->make(CreateDiscountCodeValidator::class)->validate($data);

            if ( ! $uploadedImage = $req->getUploadedFiles()["image"] ) {
                return $res->withJson([
                    "message" => "you must upload image",
                ],400);  
            }

            $uploadedImage = $this->imagesService->save($uploadedImage);
            $uploadedImage = $this->imagesRepository->addImage($uploadedImage);

            $dcode = $this->discountCodeRepository->createDiscountCode(
                (string) $data["code"],
                (int) $data["precentage"],
                (bool) $data["valid"],
                (bool) $data["promoted"],
                $uploadedImage
            );

            $res = $res->withJson([
                "status" => "success",
                "message" => "discount code created successfully",
                "discount-code-id" => $dcode->getId()
            ]);
            
        }catch(UniqueConstraintViolationException $e){
            $res = $res->withJson([
                "status" => "failure",
                "message"=> "the discount code is already exist"
            ],400);
        }catch(RequestValidatorException $e){
            $res = $res->withJson($e,400);
        }catch(UploadedFileException $e) {
            $res = $res->withJson(["message"=>$e->getMessage()],400);
        }

        return $res;
    }


    function updateDiscountCode(ServerRequest $req, Response $res,$args) {
        $id = $args["id"];
        $data = $req->getParsedBody();
        try {

            $this->validatorFactory->make(CreateDiscountCodeValidator::class)->validate($data);
            
            /** @var DiscountCode */
            $discountCode = $this->discountCodeRepository->find($id);


            $oldImage = $discountCode->getImage() !== null ? $discountCode->getImage()->getFullFileName() : null;

            if (  $uploadedImage = $req->getUploadedFiles()["image"] ) {
                $uploadedImage = $this->handleImage($uploadedImage);
            } else {
                $uploadedImage = null;
            }

            $dcode = $this->discountCodeRepository->updateDiscountCode(
                $id,
                (string) $data["code"],
                (int) $data["precentage"],
                (bool) $data["valid"],
                (bool) $data["promoted"],
                $uploadedImage
            );

            if($uploadedImage) {
                if($oldImage) {
                    $this->imagesService->deleteImage($oldImage);
                }
            }

            $res = $res->withJson([
                "status" => "success",
                "message" => "discount code updated successfully",
                "discount-code-id" => $dcode->getId()
            ]);
            
        }catch(UniqueConstraintViolationException $e){
            $res = $res->withJson([
                "status" => "failure",
                "message"=> "the discount code is already exist"
            ],400);
        }catch(RequestValidatorException $e){
            $res = $res->withJson($e,400);
        }catch(EntityNotExistException $e){
            $res = $res->withJson(["message" => $e->getMessage()]);
        }

        return $res;

    }

    function getDiscountCodes(ServerRequest $req, Response $res,$args){
        $repo = $this->discountCodeRepository;

        $codes = $repo->getDiscountCodes();

        return $res->withJson($codes);
    }

    private function handleImage(UploadedFile $image) {
        $uploadedImage = $this->imagesService->save($image);
        $uploadedImage = $this->imagesRepository->addImage($uploadedImage);
        return $uploadedImage;
    }

    private function getDeleteOldImagesClosure() {
        $imagesService = $this->imagesService;
        return $deletingOldImages = function (DiscountCode $discountCode) use ($imagesService) {
            if($image = $discountCode->getImage()) {
                if($imagesService->deleteImage($image->getFullFileName())) {
                    return true;
                }
            }
        };
    }
    

}