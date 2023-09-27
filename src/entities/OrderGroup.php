<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[Entity()]
#[ORM\Table(name:"orders_group")]
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
    private int $status = 2;

    #[ORM\OneToOne(targetEntity:OrderGroup::class,mappedBy:"orderGroup")]
    #[ORM\JoinColumn(name:"deliveryData_id",referencedColumnName:"id",nullable:true)]
    private DeliveryData | null $deliveryData;
    

    #[ORM\ManyToOne(targetEntity:User::class,inversedBy:"orders",cascade:["persist","remove"])]
    private User $user;


    #[ORM\ManyToOne(targetEntity:DiscountCode::class,inversedBy:"orderGroups",cascade:["persist","remove"])]
    #[ORM\JoinColumn(name:"discountCode_id",referencedColumnName:"id",nullable:true)]
    private DiscountCode | null $discountCode;
    
    function __construct(){
        $this->orders = new ArrayCollection();
    }

    function addOrder(Order $order) {
        $this->orders->add($order);
        $order->setOrderGroup($this);
    }

    function getTotal(){
        $price = 0;
        /**
         * @var Order $order
         */
        foreach($this->orders as $order) {
            $price += $order->getTotal();
        }
        if(isset($this->deliveryData)){
            $price += $this->deliveryData->getDeliveryFee();
        }
        if(isset($this->discountCode) && $this->discountCode->isValid()) {
            $price -= $this->discountCode->getAmount();
        }
        return $price;
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
     * Get the value of deliveryData
     */
    public function getDeliveryData(): DeliveryData
    {
        return $this->deliveryData;
    }

    /**
     * Set the value of deliveryData
     */
    public function setDeliveryData(DeliveryData $deliveryData): self
    {
        $this->deliveryData = $deliveryData;

        return $this;
    }

    /**
     * Get the value of discountCode
     */
    public function getDiscountCode(): DiscountCode
    {
        return $this->discountCode;
    }

    /**
     * Set the value of discountCode
     */
    public function setDiscountCode(DiscountCode $discountCode): self
    {
        $this->discountCode = $discountCode;

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