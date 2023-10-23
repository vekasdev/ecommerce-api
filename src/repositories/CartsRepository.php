<?php


namespace App\repositories;
use Doctrine\ORM\EntityRepository;
use Cart;
use Order;
use PhpParser\Node\Stmt\Return_;
use User;

class CartsRepository extends EntityRepository{
    function createCart(User $user) : Cart{
        $cart = new Cart();
        $user->addCart($cart);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->persist($cart);
        $this->getEntityManager()->flush();
        return $cart;
    }

    function addOrderToCart(Cart $cart,Order $order) : Cart{
        if($cart->isContainOrder($order)) {
            $cart->removeOrder($order);
            $order->increaseQuantity();
        }
        $cart->addOrder($order);
        $this->getEntityManager()->persist($cart);
        $this->getEntityManager()->flush();
        return $cart;
    }

    function getDetails(Cart $cart) {
        $qb = $this->createQueryBuilder("ca");
        $qb->select("ca,o,pr")
            ->leftJoin("ca.orders","o")
            ->leftJoin("o.product","pr")
            ->where($qb->expr()->eq("ca.id",":id"));
        $qb->setParameter("id",$cart->getId());

        $entity = $qb->getQuery()->getSingleResult();

        $result = $qb->getQuery()->getArrayResult()[0];
        

        // add total to the query
        $result = [...$result , "total" => $entity->getTotal()];

        return $result;
    }
    
    
}