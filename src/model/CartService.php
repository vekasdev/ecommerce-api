<?php

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\repositories\CartsRepository;
use App\repositories\OrdersRepository;
use App\repositories\ProductsRepository;
use Cart;
use Doctrine\ORM\EntityManager;
use Order;
use Product;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class CartService {

    private CartsRepository $cartsRepository;

    private OrdersRepository $ordersRepository;

    private ProductsRepository $productsRepository;

    function __construct(
        private Cart $cart,
        private EntityManager $entityManager
    ){
        $this->cartsRepository = $entityManager->getRepository(Cart::class);
        $this->ordersRepository = $entityManager->getRepository(Order::class);
        $this->productsRepository  = $entityManager->getRepository(Product::class);
    }

    function getDetails() {
        $details =  $this->cartsRepository->getDetails($this->cart);
        return $details;
    }

    function getCart() {
        return $this->cart;
    }

    /**
     * @throws Exception
     */
    function addOrder($order) : void {
        if( $order instanceof Order ) {
            $orderObj = $order;
        } else if ( is_int($order) ) {
            if( !$orderObj = $this->ordersRepository->find( $order ) )
                throw new EntityNotExistException(" the order with id `$order` not exist ");
        } else {
            throw new InvalidParameterException();
        }


        // if order exist increase its quantity 
        foreach ( $this->cart->getOrders() as $cartOrder ) {
            if ( $orderObj->getProduct() == $cartOrder->getProduct() ) {
                // increase quantity by 1
                $cartOrder->changeQuantity($orderObj->getQuantity());

                // persist changes
                $this->entityManager->persist($cartOrder);
                $this->entityManager->remove($orderObj);
                $this->entityManager->flush();

                return ;
            }
        }


        // if not exist add to cart
        $this->cart = $this->cartsRepository->addOrderToCart($this->cart,$orderObj);
    }

    function changeOrderQuantity($order , $num = 1 ) {
        if( $order instanceof Order ) {
            $orderObj = $order;
        } else if ( is_int($order) ) {
            if( !$orderObj = $this->ordersRepository->find( $order ) )
                throw new EntityNotExistException(" the order with id `$order` not exist ");
        } else {
            throw new InvalidParameterException();
        }

        $orderObj->changeQuantity($num);
        
        if($orderObj->getQuantity() < 1) {
            $this->getCart()->removeOrder($orderObj);
            $this->entityManager->persist($this->getCart());
            $this->entityManager->remove($orderObj);
            $this->entityManager->flush();
            return ;
        }

        $this->entityManager->persist($orderObj);
        $this->entityManager->flush();
        return;
    }

    function createOrder($product,$quantity) {
        if( $product instanceof Product ) {
            $productObj = $product;
        } else if ( is_int($product) ) {
            if( !$productObj = $this->productsRepository->find( $product ) )
                throw new EntityNotExistException(" the product with id `$product` not exist ");
        } else {
            throw new InvalidParameterException();
        }


        $order = $this->ordersRepository->createOrder($productObj,$quantity);
        $this->addOrder($order);
    }
}