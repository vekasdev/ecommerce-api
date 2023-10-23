<?php

use App\repositories\CartsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass:CartsRepository::class)]
#[ORM\Table(name:"carts")]
class Cart {

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\OneToMany(targetEntity:Order::class,mappedBy:"cart",cascade:["persist"])]
    private $orders;


    #[ORM\OneToOne(targetEntity:OrderGroup::class,inversedBy:"cart",cascade:["persist","remove"])]
    #[ORM\JoinColumn(name:"order_group_id",referencedColumnName:"id",nullable:true)]
    private OrderGroup $orderGroup;


    #[ORM\ManyToOne(targetEntity:User::class,inversedBy:"carts")]
    private User $user;

    #[ORM\Column(type:Types::BOOLEAN)]
    private bool $processed = false;

    function __construct(){
        $this->orders = new ArrayCollection;
    }
    function addOrder(Order $order){
        $this->orders->add($order);
        $order->setCart($this);
    }

    function getOrders() {
        return $this->orders;
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
     * Get the value of processed
     */
    public function isProcessed(): bool
    {
        return $this->processed;
    }

    /**
     * Set the value of processed
     */
    public function setProcessed(bool $processed): self
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get the value of user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set the value of user
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}