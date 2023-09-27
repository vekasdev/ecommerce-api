<?php


namespace App\repositories;
use Doctrine\ORM\EntityRepository;
use Cart;
use PhpParser\Node\Stmt\Return_;

class CartsRepository extends EntityRepository{
    function createCart() : Cart{
        $cart = new Cart();
        $this->getEntityManager()->persist($cart);
        $this->getEntityManager()->flush();
        return $cart;
    }

    /**
     * @param \Order[] $orders
     */
    function addOrdersToCart(Cart $cart,array $orders) : Cart{
        foreach($orders as $order) {
            if($cart->isContainOrder($order)) {
                $cart->removeOrder($order);
                $order->multiplyQuantities();
            }
            $cart->addOrder($order);
        }
        $this->getEntityManager()->persist($cart);
        $this->getEntityManager()->flush();
        return $cart;
    }
}