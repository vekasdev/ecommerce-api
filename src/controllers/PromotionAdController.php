<?php


namespace App\controllers;

use App\dtos\ResponseDataTransfer;
use App\dtos\UserData;
use App\dtos\UserFiltering;
use App\exceptions\EntityNotExistException;
use App\exceptions\RequestValidatorException;
use App\exceptions\UploadedFileException;
use App\exceptions\UserValidationException;
use App\model\AuthinticationService;
use App\model\ImagesService;
use App\model\PromotionAdServiceProvider;
use App\model\UserServiceFactory;
use App\model\ValidatorFactory;
use App\repositories\ImagesRepository;
use App\repositories\PromotionAdsRepository;
use App\repositories\UsersRepository;
use App\validators\AddPromotionAdValidator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Image;
use PromotionAd;
use responses\JsonResponse;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use User;
use Vekas\ResponseManager\ResponseManager;

class PromotionAdController { 
    private ImagesRepository $imagesRepository;

    private PromotionAdsRepository $promotionAdsRepository;
    function __construct(
        private EntityManager $em,
        private ImagesService $imagesService,
        private PromotionAdServiceProvider $promotionAdServiceProvider,
        private ValidatorFactory $validatorFactory
    ) {
        $this->imagesRepository  = $em->getRepository(Image::class);
        $this->promotionAdsRepository = $em->getRepository(PromotionAd::class);
    }

    function addPromotionAd(ServerRequest $req, Response $res,$args) {

        try{
            $data = $req->getParsedBody();
            $this->validatorFactory->make(AddPromotionAdValidator::class)->validate($data);

            if(isset($req->getUploadedFiles()["image"])) {
                $image = $req->getUploadedFiles()["image"];
                $image= $this->imagesService->save($image);
                $image= $this->imagesRepository->addImage($image);
            } else {
                throw new RequestValidatorException(["image" => "required"]);
            }
            $promotionAd = $this->promotionAdsRepository->create($image,(bool)$data["main"]);
            $res = $res->withJson([
                "status" => "success",
                "message" => "promotionAd created successfully",
                "data" => [
                    "id" => $promotionAd->getId(),
                    "is-main" => $promotionAd->isMain(),
                    "image-file-name" => $promotionAd->getImages()->first()->getFileName(),
                    "image-file-extention" => $promotionAd->getImages()->first()->getExtension(),
                ]
            ]);
        } catch (EntityNotExistException $e) { 
            $res = $res->withJson(["error"=> $e->getMessage()]);
        } catch (RequestValidatorException $e) {
            $res = $res->withJson($e,400);
        } catch (UploadedFileException $e) {
            $res = $res->withJson(["status"=>"failure","message" => $e->getMessage()],400);
        }


        return $res;
    }

    function getPromotionAds(ServerRequest $req, Response $res,$args) {
        $data = $this->promotionAdsRepository->getAll();
        return $res->withJson($data);
    }

    function updatePromotionAd(ServerRequest $req, Response $res,$args) {
        try{
            $id = (int) $args["id"];
            $service = $this->promotionAdServiceProvider->make($id);
    
            $data = $req->getParsedBody();
            $this->validatorFactory->make(AddPromotionAdValidator::class)->validate($data);

            if(isset($req->getUploadedFiles()["image"])) {
                $image = $req->getUploadedFiles()["image"];
                $image= $this->imagesService->save($image);
                $image= $this->imagesRepository->addImage($image);
            } else {
                throw new RequestValidatorException(["image" => "required"]);
            }
            
            $service->update($image,(bool) $data["main"]);

            $res = $res->withJson([
                "status" => "success",
                "message" => "promotionAd updated successfully",
                "data" => [
                    "id" => $service->getEntity()->getId(),
                    "is-main" => $service->getEntity()->isMain(),
                    "image-file-name" => $service->getEntity()->getImages()->first()->getFileName(),
                    "image-file-extention" => $service->getEntity()->getImages()->first()->getExtension(),
                ]
            ]);
        } catch (EntityNotExistException $e) { 
            $res = $res->withJson(["error"=> $e->getMessage()]);
        } catch (RequestValidatorException $e) {
            $res = $res->withJson($e,400);
        } catch (UploadedFileException $e) {
            $res = $res->withJson(["status"=>"failure","message" => $e->getMessage()],400);
        }
        return $res;
    }

    function deletePromotionAd(ServerRequest $req, Response $res,$args) {
        try {
            $id = (int) $args["id"];
            $service = $this->promotionAdServiceProvider->make($id);
            $service->remove();
            $res = $res->withJson([
                "status" => "success",
                "message" => "promotionAd deleted successfully",
            ]);
        } catch (EntityNotExistException $e) { 
            $res = $res->withJson(["message"=> $e->getMessage()]);
        } 

        return $res;
    }

}