<?php 

namespace App\repositories;
use Doctrine\ORM\EntityRepository;
use Product;
use \Order;

class OrdersRepository extends EntityRepository {
    function createOrder(Product $product , int $quantity){
        $order = new Order();
        $order->setProduct($product);
        $order->setQuantity($quantity);
        $this->getEntityManager()->persist($order);
        $this->getEntityManager()->flush();
        return $order;
    }
}