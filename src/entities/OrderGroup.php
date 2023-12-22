<?php

use App\model\OrderGroupStatus;
use App\model\OrderTypeStatus;
use App\repositories\OrderGroupsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[Entity(repositoryClass:OrderGroupsRepository::class)]
#[ORM\Table(name:"orders_group")]
class OrderGroup {

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    private $id;


    #[Column(type:Types::DATETIME_MUTABLE,options:["default"=>"CURRENT_TIMESTAMP"])]
    private  $date;

    #[Column(type:Types::INTEGER)]
    private int $status = OrderGroupStatus::NOT_INITIALIZED;

    #[ORM\ManyToMany(targetEntity:DeliveryData::class,mappedBy:"orderGroup",cascade:["persist"])]
    #[ORM\JoinColumn(name:"deliveryData_id",referencedColumnName:"id",nullable:true)]
    private  $deliveryData;
    

    #[ORM\ManyToOne(targetEntity:User::class,inversedBy:"orders",cascade:["persist","remove"])]
    private User $user;

    #[ORM\ManyToOne(targetEntity:DiscountCode::class,inversedBy:"orderGroups",cascade:["persist"])]
    #[ORM\JoinColumn(name:"discountCode_id",referencedColumnName:"id",nullable:true)]
    private DiscountCode | null $discountCode = null;

    #[ORM\OneToOne(targetEntity:Cart::class,inversedBy:"orderGroup",cascade:["remove"])]
    private Cart $cart;
    
    function __construct(){
        $this->date = new DateTime();
        $this->deliveryData  = new ArrayCollection();
    }
    

    function getTotal(){
        $total = $this->cart->getTotal();

        if(isset($this->discountCode) && $this->discountCode->isValid()) {
            $discountAmount = $total * $this->discountCode->getPrecentage();
            $total -= $discountAmount;
        }
        
        return $total + $this->getDeliveryCost();
    }

    function getDeliveryCost() {
        $cost = 0.0;

        if($this->getDeliveryData()) {
            $cost = $this->getDeliveryData()->getDeliveryCost();
        }

        return  $cost;
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
    public function getDeliveryData(): DeliveryData | null
    {
        $dd = $this->deliveryData->first();
        return !is_bool($dd) ? $dd : null;
    }

    /**
     * Set the value of deliveryData
     */
    public function setDeliveryData(DeliveryData $deliveryData): self
    {
        $this->deliveryData->clear();
        $this->deliveryData->add($deliveryData);
        $deliveryData->setOrderGroup($this);

        return $this;
    }

    /**
     * Get the value of discountCode
     */
    public function getDiscountCode(): DiscountCode | null
    {
        return is_null($this->discountCode) ? null : $this->discountCode;
    }

    /**
     * Set the value of discountCode
     */
    public function setDiscountCode(DiscountCode $discountCode): self
    {
        $this->discountCode = $discountCode;
        $discountCode->addOrderGroup($this);
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

    /**
     * Get the value of cart
     */
    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * Set the value of cart
     */
    public function setCart(Cart $cart): self
    {
        $this->cart = $cart;
        $this->cart->setOrderGroup($this);
        return $this;
    }
}