<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
#[Entity()]
class OrderGroup {

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\OneToMany(targetEntity:Order::class,mappedBy:"orderGroup")]
    private $orders;

    #[Column(type:Types::DATETIME_MUTABLE)]
    private DateTime $date;

    #[Column(type:Types::INTEGER)]
    private int $status;
    function __construct(){
        $this->orders = new ArrayCollection();
    }

    function addOrder(Order $order) {
        $this->orders->add($order);
        $order->setOrderGroup($this);
    }

    function getTotal(){

    }

    /**
     * Get the value of status
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}