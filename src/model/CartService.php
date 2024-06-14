<?php

namespace App\model;

use App\exceptions\EntityNotExistException;
use App\repositories\CartsRepository;
use App\repositories\ColorsRepository;
use App\repositories\OrdersRepository;
use App\repositories\ProductsRepository;
use Cart;
use Color;
use Doctrine\ORM\EntityManager;
use Order;
use Product;
use Symfony\Component\Routing\Exception\InvalidParameterException;

use function PHPUnit\Framework\isNull;

class CartService {

    private CartsRepository $cartsRepository;

    private OrdersRepository $ordersRepository;

    private ProductsRepository $productsRepository;

    private ColorsRepository $colorRepository;

    function __construct(
        private Cart $cart,
        private EntityManager $entityManager
    ){
        $this->cartsRepository = $entityManager->getRepository(Cart::class);
        $this->ordersRepository = $entityManager->getRepository(Order::class);
        $this->productsRepository  = $entityManager->getRepository(Product::class);
        $this->colorRepository = $entityManager->getRepository(Color::class);
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

        foreach ( $this->cart->getOrders() as $cartOrder ) {
            if ( $orderObj->getProduct() == $cartOrder->getProduct() ) {
                // if the product and color are the same increase quantity by 
                $newColor = $orderObj->getColor();
                $oldColor = $cartOrder->getColor();
                if ( $newColor === $oldColor ) {
                    $cartOrder->changeQuantity($cartOrder->getQuantity() + $orderObj->getQuantity());
                    $this->entityManager->persist($cartOrder);
                    $this->entityManager->remove($orderObj);
                    $this->entityManager->flush();
                    return;
                }
            }
        }
        // if not exist ( product ) and same color add to cart
        $this->cart = $this->cartsRepository->addOrderToCart($this->cart,$orderObj);
    }

    function changeOrderQuantity($order , $num ) {
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

    function createOrder($product,$quantity,int | null $color) {
        if( $product instanceof Product ) {
            $productObj = $product;
        } else if ( is_int($product) ) {
            if( !$productObj = $this->productsRepository->find( $product ) )
                throw new EntityNotExistException(" the product with id `$product` not exist ");
        } else {
            throw new InvalidParameterException();
        }

        if( $color instanceof Color ) {
            $colorObj = $color;
        } else if ( is_int($color) ) {
            if( !$colorObj = $this->colorRepository->find( $color ) )
                throw new EntityNotExistException(" color with id `$color` not exist ");
        } elseif ( isNull($color) ) {
            $colorObj = null;
        } else {
            throw new InvalidParameterException();
        }
        
        $order = $this->ordersRepository->createOrder($productObj,$quantity,$colorObj);
        $this->addOrder($order);
    }

    function getOrdersCount() {
        $count = 0;
        /** @var Order $order */
        foreach($this->cart->getOrders() as $order){
            $count += $order->getQuantity();
        }

        return $count;
    }


}