<?php

namespace App\controllers;
use App\dtos\DeliveryDataDTO;
use App\dtos\ResponseDataTransfer;
use App\exceptions\ElementAlreadyExistsException;
use App\exceptions\EntityNotExistException;
use App\exceptions\OrderingProcessException;
use App\exceptions\ProcessedRequestException;
use App\exceptions\RequestValidatorException;
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
use App\repositories\OrderGroupsRepository;
use App\validators\CreateDiscountCodeValidator;
use App\validators\GetOrderGroupsValidator;
use App\validators\SetDeliveryDataValidator;
use DeliveryRegion;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class DiscountCodeController {
    private DiscountCodeRepository $discountCodeRepository;
    function __construct(
        private EntityManager $em,
        private ValidatorFactory $validatorFactory
    
    ) {
        $this->discountCodeRepository = $em->getRepository(DiscountCode::class);
    }

    function createDiscountCode(ServerRequest $req, Response $res,$args) {
        $id = $args["id"];
        $data = $req->getParsedBody();

        try {

            $this->validatorFactory->make(CreateDiscountCodeValidator::class)->validate($data);

            $dcode = $this->discountCodeRepository->createDiscountCode(
                (string) $data["code"],
                (int) $data["precentage"],
                (bool) $data["valid"],
                (bool) $data["promoted"]);

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
        }

        return $res;
    }


    function updateDiscountCode(ServerRequest $req, Response $res,$args) {
        $id = $args["id"];
        $data = $req->getQueryParams();

        try {

            $this->validatorFactory->make(CreateDiscountCodeValidator::class)->validate($data);

            $dcode = $this->discountCodeRepository->updateDiscountCode(
                $id,
                (string) $data["code"],
                (int) $data["precentage"],
                (bool) $data["valid"],
                (bool) $data["promoted"]);
                
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

}