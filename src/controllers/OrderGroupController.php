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
use App\repositories\OrdersRepository;
use App\validators\AddOrderValidator;
use Cart;
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
use App\validators\GetOrderGroupsValidator;
use App\validators\SetDeliveryDataValidator;
use DeliveryRegion;


class OrderGroupController { 
    private OrderGroupsRepository $orderGroupRepo ;
    function __construct(
        private EntityManager $em,
        private OrderGroupServiceFactory $orderGroupServiceFactory,
        private ValidatorFactory $validatorFactory
    
    ) {
        $this->orderGroupRepo = $em->getRepository(OrderGroup::class);
    }

    function getOrderGroups(ServerRequest $req, Response $res,$args ) {
        try {
            $params = $req->getQueryParams();
            $params["withInformation"] = true;

            $this->validatorFactory->make(GetOrderGroupsValidator::class)
            ->validate($params);

            $orders = $this->orderGroupRepo->getOrderGroups($params);

            $res = $res->withJson($orders);
        } catch (RequestValidatorException $e ) { 
            $res = $res->withJson($e,400);
        }
        return $res;
    }

    function markAsDelivered(ServerRequest $req, Response $res,$args) {
        $orderGroupId = (int) $args["id"];
        try {
            $orderService = $this->orderGroupServiceFactory->make($orderGroupId);
            $orderService->toDeliveredState();
            $res = $res->withJson(
                [
                    "status" => "success",
                    "message"=> "order group set as delivered with id : ".$orderService->getEntity()->getId()
                ],
                200
            );
        } catch (EntityNotExistException $e) {
            $res = $res->withJson(["error"=> $e->getMessage()],400);
        } catch (ProcessedRequestException  $e) {
            $res = $res->withJson(["error"=> $e->getMessage()],400);
        }

        return $res;
    }

    function dismissOrder(ServerRequest $req, Response $res,$args) {
        $id = $args["id"];
        try {
            $orderService = $this->orderGroupServiceFactory->make((int) $id);
            $result = $orderService->dismiss();
            if($result) {
                $res= $res->withJson([
                    "message" => "order with id $id dismissed successfully"
                ]);
            } else {
                $res= $res->withJson([
                    "message" => "order with id $id is already dismissed"
                ],400);
            }
        } catch (EntityNotExistException $e) {
            $res = $res->withJson(["message"=> $e->getMessage()],400);
        }

        return $res;
    }

    // this is required a normal-user authintication , its get's the order groups that are in it's own
    function getCurrentUserOrderGroups(ServerRequest $req, Response $res,$args) {
        /** @var UserService $userService */
        $userService = $req->getAttribute("user");
        $status = $req->getQueryParam("status");
        $results = [];
        switch($status) {
            case "1" : 
                $results = $this->orderGroupRepo->getOrderGroups([
                    "id" => $userService->getUser()->getId(),
                    "status"  => $status,
                    "withInformation" => false
                ]);
                break;
            case "2": 
                $results = $this->orderGroupRepo->getOrderGroups([
                    "id" => $userService->getUser()->getId(),
                    "status"  => $status
                ]);
                break;
            case "3" :
                $results = $this->orderGroupRepo->getOrderGroups([
                    "id" => $userService->getUser()->getId(),
                    "status"  => $status,
                    "withInformation" => false
                ]);
                break;
            default : 
                $results = $this->orderGroupRepo->getOrderGroups([
                    "id" => $userService->getUser()->getId(),
                    "withInformation" => false
                ]);
                break ;
        }
        return $res->withJson($results);
    }

}