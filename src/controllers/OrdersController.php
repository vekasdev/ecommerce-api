<?php


namespace App\controllers;

use App\dtos\DeliveryDataDTO;
use App\dtos\ResponseDataTransfer;
use App\exceptions\ElementAlreadyExistsException;
use App\exceptions\EntityNotExistException;
use App\exceptions\OrderingProcessException;
use App\exceptions\RequestValidatorException;
use App\model\ValidatorFactory;
use App\repositories\CartsRepository;
use App\repositories\OrdersRepository;
use App\validators\AddOrderValidator;
use Cart;
use Doctrine\ORM\EntityManager;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Vekas\ResponseManager\ResponseManager;
use Order;
use responses\JsonResponse;
use App\model\UserService;
use App\repositories\DeliveryRegionRepository;
use App\validators\SetDeliveryDataValidator;
use DeliveryRegion;
class OrdersController {
    private OrdersRepository $ordersRepository;
    private CartsRepository $cartsRepository;
    private DeliveryRegionRepository $deliveryRegionRepository;
    function __construct(
        private EntityManager $entityManager,
        private ResponseManager $responseManager,
        private ValidatorFactory $validatorFactory
    ) {
        $this->ordersRepository = $entityManager->getRepository(Order::class);
        $this->cartsRepository = $entityManager->getRepository(Cart::class);
        $this->deliveryRegionRepository = $entityManager->getRepository(DeliveryRegion::class);
    }
    function createOrder(ServerRequest $req, Response $res,$args){

        /**
         * @var UserService
         */

        $userService = $req->getAttribute("user");

        $cartService = $userService->getCartService();

        try {
            $cartService->createOrder((int)$args["product-id"],(int) $args["quantity"]);
        }catch(EntityNotExistException $e) {
            return $res->withJson(
                ["message" => $e->getMessage()],
                400);
        }

        return $res->withJson(
            ["message" => "order created"],200
        );

    }

    function changeOrderQty(ServerRequest $req, Response $res,$args) {

        // orderId , valueOfChange
        /**
         * @var UserService
         */
         $userService = $req->getAttribute("user");
         
         $valueOfChange = (int) $args["valueOfChange"];

         $orderId = (int) $args["orderId"];

         $userService->getCartService()->changeOrderQuantity($orderId,$valueOfChange);


        return $res->withJson(
            $userService->getCartService()->getDetails(),200
        );
    }


    function getOrderGroup(ServerRequest $req, Response $res,$args) {
        $cart = $this->cartsRepository->find($args["cart-id"]);
        if(!$cart) {
            $resObj = new ResponseDataTransfer($res,400,[
                "status" => "failure",
                "message" => "cart is not exist"
            ]);
        }
    }

    function getCartDetails(ServerRequest $req, Response $res,$args) {
        /**
         * @var UserService
         */
        $userService = $req->getAttribute("user");

        $details = $userService->getCartService()->getDetails();
        
        return $res->withJson($details,200);
    }


    function confirmCart(ServerRequest $req, Response $res,$args) {
        /**
         * @var UserService
         */
        $userService = $req->getAttribute("user");

        $orderService = $userService->getOrderGroupService();
        
        try {
            $orderService->processOrdering();
            return $res->withJson([
                "status"=> "success",
                "message" => "items are ordered and its state changed to pending"
            ],200);
        } catch (OrderingProcessException $e) {
            return $res->withJson([
                "message"=> $e->getMessage()
            ],400);
        }

    }

    function setDeliveryData(ServerRequest $req, Response $res,$args) {
        /**
         * @var UserService
         */
        $userService = $req->getAttribute("user");
        $orderService = $userService->getOrderGroupService();
        $data = $req->getParsedBody();
        try {
            $this->validatorFactory->make( SetDeliveryDataValidator::class )->validate( $data );
            $deliveryData = $orderService->setDeliveryData( $this->getDeliveryDataDTO($data) );
            $res =  $res->withJson([["message"=> "delivery data set with id : " . $deliveryData->getId()]],200 );
        } catch (RequestValidatorException $e) {
            $res = $res->withJson($e,400);
        } catch (EntityNotExistException $e) {   
            $res = $res->withJson(["message" => $e->getMessage() ],400);
        }
        return $res;
    }


    function addDiscountCode(ServerRequest $req, Response $res,$args) {
        /**
         * @var UserService
         */
        $userService = $req->getAttribute("user");
        $orderGroupService = $userService->getOrderGroupService();


        $code = $args["code"];

        try {
            $orderGroupService->addDiscountCode($code);
            $res = $res->withJson([[
                "message"=> "discount added successfully",
                "status"=>"success"] ,200]);
        } catch (EntityNotExistException $e) {
            $res = $res->withJson(
                [
                    "status"=>"failed",
                    "message" => $e->getMessage()
                ],
            400);
        } catch (ElementAlreadyExistsException $e) {
            $res = $res->withJson(
                [
                    "status"=>"failed",
                    "message" => $e->getMessage()
                ],
            400);
        }

        return $res;
    }

    function getCost( ServerRequest $req, Response $res,$args) {
        /**
         * @var UserService
         */
        $userService = $req->getAttribute("user");
        $orderGroupService = $userService->getOrderGroupService();

        $total = $orderGroupService->getTotal();

        return $res->withJson([
            "discounted" => $total->discounted,
            "previousPrice" =>  $total->previousPrice,
            "currentPrice" => $total->currentPrice
        ],200);
    }
    
    /**
     * @throws EntityNotExistException
     */
    private function getDeliveryDataDTO(array $data)  : DeliveryDataDTO{
            if( ($region = $this->deliveryRegionRepository->find($data["region"])) ==null)
            throw new EntityNotExistException("region with id : ".$data["region"]." not exist");
        return new DeliveryDataDTO(
            $data["name"],
            $data['phone-number'],
            $data['location'],
            $region,
            $data['postal-code'],
            $data['maps-location'],
            (isset($data["defaultData"])) & ($data["defaultData"] ==1) ? true : false,
            (isset($data["delivery"])) & ($data["delivery"] ==1) ? true : false,
        );
    }

}