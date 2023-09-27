<?php

use App\repositories\OrdersRepository;
use Doctrine\DBAL\Driver\Mysqli\Initializer\Options;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass:OrdersRepository::class)]
#[ORM\Table(name:"orders")]

class Order {

    function __construct(){
        $this->date = new DateTime;
    }

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\ManyToOne(targetEntity:Product::class,inversedBy:"orders",cascade:["persist","remove"])]
    private Product $product;

    #[Column(type:Types::DATETIME_MUTABLE)]
    private DateTime $date ;

    #[Column(type:Types::INTEGER)]
    private int $quantity;
    

    #[ORM\ManyToOne(targetEntity:OrderGroup::class,inversedBy:"orders",cascade:["persist","remove"])]
    #[ORM\JoinColumn(name:"orderGroup_id",referencedColumnName:"id",nullable:true)]
    private OrderGroup | null $orderGroup;


    #[ORM\ManyToOne(targetEntity:Cart::class,inversedBy:"orders",cascade:["persist","remove"])]
    #[ORM\JoinColumn(name:"Cart_id",referencedColumnName:"id",nullable:true)]
    private Cart $cart;

    function multiplyQuantities() {
        $this->quantity  = $this->quantity * 2;
    }
    function getTotal() : float{
        return $this->getProduct()->getPrice() * $this->getQuantity(); 
    }
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

    /**
     * Get the value of date
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * Set the value of date
     */
    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of quantity
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of cart
     */
    public function getCart(): Cart | null
    {
        return $this->cart ?? null ;
    }

    /**
     * Set the value of cart
     */
    public function setCart(Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }
}