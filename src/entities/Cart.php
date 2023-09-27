<?php

use App\repositories\CartsRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass:CartsRepository::class)]
#[ORM\Table(name:"carts")]
class Cart {

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\OneToMany(targetEntity:Order::class,mappedBy:"cart")]
    private $orders;


    function addOrder(Order $order){
        $this->orders->add($order);
        $order->setCart($this);
    }

    function isContainOrder(Order $order) {
        if($this->orders->contains($order))return true;
        return false;
    }

    function removeOrder(Order $order){
        $this->orders->removeElement($order);
    }

    function clearCart(){
        $this->orders->clear();
    }

    function getTotal(){
        $price = 0;
        /**
         * @var Order $order
         */
        foreach($this->orders as $order) {
            $price += $order->getTotal();
        }
        return $price;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }
}