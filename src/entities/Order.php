<?php

use Doctrine\DBAL\Driver\Mysqli\Initializer\Options;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity()]
class Order {

    function __construct(){
    
    }

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\ManyToOne(targetEntity:Product::class,inversedBy:"orders")]
    private Product $product;

    #[Column(type:Types::DATETIME_MUTABLE)]
    private DateTime $date;

    #[Column(type:Types::INTEGER)]
    private $quantity;

    #[ORM\ManyToOne(targetEntity:OrderGroup::class,inversedBy:"orders",cascade:["persist","remove"])]
    private OrderGroup $orderGroup;
    

    /**
     * Get the value of orderGroup
     */
    public function getOrderGroup(): OrderGroup
    {
        return $this->orderGroup;
    }

    /**
     * Set the value of orderGroup
     */
    public function setOrderGroup(OrderGroup $orderGroup): self
    {
        $this->orderGroup = $orderGroup;

        return $this;
    }

    /**
     * Get the value of product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * Set the value of product
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}