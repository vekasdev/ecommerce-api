<?php

namespace App\controllers;
use App\dtos\DeliveryDataDTO;
use App\dtos\DeliveryRegionDataDTO;
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
use App\validators\AddDeliveryRegionValidator;
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
use App\validators\FilteringDeliveryRegionValidator;
use App\validators\GetOrderGroupsValidator;
use App\validators\SetDeliveryDataValidator;
use DeliveryRegion;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class DeliveryRegionController {
    private DeliveryRegionRepository $deliveryRegionRepository;
    function __construct(
        private EntityManager $em,
        private ValidatorFactory $validatorFactory
    
    ) {
        $this->deliveryRegionRepository = $em->getRepository(DeliveryRegion::class);
    }


    function getDeliveryRegions(ServerRequest $req, Response $res,$args) {
        $data = $this->deliveryRegionRepository->getAll();
        return $res->withJson($data);
    }

    function addDeliveryRegion(ServerRequest $req, Response $res,$args) {
        try {
            $data = $req->getParsedBody();
            $this->validatorFactory->make(AddDeliveryRegionValidator::class)->validate($data);
            $dg = $this->deliveryRegionRepository->save($data["region"],$data["cost"],(bool) $data["available"]);
            $res = $res->withJson([
                "status" => "success",
                "message" => "region added successfully",
                "region-id" => $dg->getId()
            ]);
        } catch (UniqueConstraintViolationException $e) {
            $res = $res->withJson([
                "status" => "failure",
                "message" => "region is already exist"
            ],400);
        } catch (RequestValidatorException $e) { 
            $res = $res->withJson($e,400);
        }

        return $res;
    }

    function updateDeliveryRegion(ServerRequest $req, Response $res,$args) {
        $id = $args['id'];
        $data = $req->getParams();
        
        try {
        $this->validatorFactory->make(AddDeliveryRegionValidator::class)->validate($data);
        $this->deliveryRegionRepository->update($id, $data["region"],
                                                (float) $data["cost"],
                                                (bool) $data["available"]);
        $res = $res->withJson(["status"=> "success","message"=> "region updated successfully","id"=> $id]);
        } catch (UniqueConstraintViolationException $e) {
            $res = $res->withJson(["status"=>"failure","message"=> "the delivery region has duplicated information"],400);
        } catch (RequestValidatorException $e) {
            $res = $res->withJson($e,400);
        } catch (EntityNotExistException $e) {
            $res = $res->withJson(["status"=> "failure","message"=> "delivery region is not exist to update"],400);
        }

        return $res;
        
    }

    function getAll( ServerRequest $req, Response $res,$args) {
        $filters = $req->getParams();
        try {
            $this->validatorFactory->make(FilteringDeliveryRegionValidator::class)->validate($filters);
            $filters = $this->getFilters($filters);
            $outcome = $this->deliveryRegionRepository->getAll($filters);
            $res = $res->withJson($outcome,200);
        } catch (RequestValidatorException $e) {
            $res = $res->withJson($e,400); 
        } 

        return $res;
    }

    function getDeliveryRegion(ServerRequest $req, Response $res,$args) {
        $id = $args["id"];
        $deliveryRegion = $this->deliveryRegionRepository->getDeliveryRegion((int) $id);
        if(!$deliveryRegion) {
            $res = $res->withJson([
                "message" => "delivery region not exist"
            ],400); 
        } else {
            $res = $res->withJson($deliveryRegion); 
        }

        return $res;
    }


    function getFilters(array $filters) : DeliveryRegionDataDTO  {
        $data = new DeliveryRegionDataDTO();
        if(isset($filters["region"])) $data->region = (string) $filters["region"];
        if(isset($filters["cost"])) $data->cost = (float) $filters["cost"];
        if(isset($filters["available"])) $data->available = (bool) $filters["available"];

        return $data;
    }
    
}
