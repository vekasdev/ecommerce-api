<?php


namespace App\controllers;

use App\dtos\ResponseDataTransfer;
use App\exceptions\EntityNotExistException;
use App\repositories\CartsRepository;
use App\repositories\OrdersRepository;
use Cart;
use Doctrine\ORM\EntityManager;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Vekas\ResponseManager\ResponseManager;
use Order;
use responses\JsonResponse;

class OrdersController {
    private OrdersRepository $ordersRepository;
    private CartsRepository $cartsRepository;
    function __construct(
        private EntityManager $entityManager,
        private ResponseManager $responseManager
    ) {
        $this->ordersRepository = $entityManager->getRepository(Order::class);
        $this->cartsRepository = $entityManager->getRepository(Cart::class);

    }

    function addOrder(ServerRequest $req, Response $res){
        // product , quantity
        $data = $req->getParsedBody();
        $product = $this->entityManager->find(\Product::class,$data["product"]);

        if(!$product) {
            // return bad requests
        }


        $order = $this->ordersRepository->addOrder($product,(int)$data["quantity"]);

        $rto = new ResponseDataTransfer($res,200,[
            "oid"   => $order->getId(),
            "total" => $order->getTotal()
        ]);
        return $this->responseManager->getResponse(JsonResponse::class,$rto);
    }

    // this is global function
    function addOrdersToCart(ServerRequest $req, Response $res){
        // orders #jsonArray , cart-id
        try {
            $params = $req->getParams();
            $orders = $this->getOrdersArray(json_decode($params["orders"]));
            $cart   = $this->cartsRepository->find((int)$params["cart-id"]);
            $cart = $this->cartsRepository->addOrdersToCart($cart,$orders);
            
            $responseData = new ResponseDataTransfer($res,200,[
                "total-amount" => $cart->getTotal(),
                "cart-id" => $cart->getId()
            ]);

        } catch (EntityNotExistException $e) {
            $responseData = new ResponseDataTransfer($res,400,[
                "message" => $e->getMessage()
            ]);
        }

        return $this->responseManager->getResponse(JsonResponse::class,$responseData);
    }

    // without validation and user requesting
    function createCart(ServerRequest $req, Response $res){
        $cart = $this->cartsRepository->createCart();
        $responseData = new ResponseDataTransfer($res,200,[
            "cart-id"     => $cart->getId(),
            "totalAmount" => $cart->getTotal()
        ]);
        return $this->responseManager->getResponse(JsonResponse::class,$responseData);
    }


    
    function getOrdersArray(array $orders){
        $orders_ = [];
        foreach($orders as $order){
            if(!$order = $this->ordersRepository->find($order)) 
                throw new EntityNotExistException("order with id $order not exist");
            array_push($orders_,$order);
        }
        return $orders_;
    }

}