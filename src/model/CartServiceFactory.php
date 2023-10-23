<?php 


namespace App\model;

use Cart;
use Doctrine\ORM\EntityManager;

class CartServiceFactory {
    function __construct(
        private EntityManager $entityManager
    ){}

    function make(Cart $cart) {
        return new CartService($cart,$this->entityManager);
    }
}