<?php 

namespace App\repositories;

use Color;
use Doctrine\ORM\EntityRepository;
use Product;
use \Order;

class OrdersRepository extends EntityRepository {
    function createOrder(Product $product , int $quantity,Color | null $color){
        $order = new Order();
        $order->setProduct($product);
        $order->setQuantity($quantity);

        if ( is_null( $color ) ) {
            $color = $order->getProduct()->getColors()->first();
        }

        $color->addOrder($order);
        $this->getEntityManager()->persist($color);
        $this->getEntityManager()->flush();
        
        return $order;
    }
}