<?php

use App\repositories\DeliveryDataRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[Entity(repositoryClass:DeliveryDataRepository::class)]
class DeliveryData {

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;
    #[ORM\Column(type:types::STRING,nullable:true)]
    private string | null $location;
    #[ORM\Column(type:types::STRING,nullable:true)]
    private string $mapsLocation;

    #[ORM\Column(type:types::STRING,nullable:true)]
    private string $phoneNumber;

    #[ORM\Column(type:Types::STRING,nullable:true)]
    private string $postalCode;

    // drive region specifies the available spaces and each-one costs

    #[ORM\ManyToOne(targetEntity:DeliveryRegion::class,inversedBy:"deliveryData")]
    #[ORM\JoinColumn(name:"deliveryRegionId",referencedColumnName:"id",nullable:true)]
    private $deliveryRegion = null;

    #[ORM\ManyToOne(targetEntity:OrderGroup::class,inversedBy:"deliveryData",cascade:["persist"])]
    private $orderGroup;

    #[ORM\ManyToOne(targetEntity:User::class,cascade:["persist","remove"],inversedBy:"deliveryData")]
    private $user;

    #[ORM\Column(type:types::BOOLEAN)]
    private bool $defaultData = false;

    #[ORM\Column(type:types::STRING,nullable:true)]
    private string | null $name;


    #[ORM\Column(type: Types::BOOLEAN,nullable:true)]
    private bool $delivery = true;
    

    function setUser(User $user) {
        $this->user = $user;
        return $this;
    }

    function getName() : null | string {
        return $this->name;
    }

    function setName(string $name) {
        $this->name = $name;
    }
    function getUser() : User{
        return $this->user;
    }

    function getDeliveryCost() {
        $cost = 0 ;
        if($this->delivery && !is_null($this->deliveryRegion)) {
            $cost = $this->deliveryRegion->getDeliveryCost();
        }

        return $cost ;
    }

    function setDeliveryRegion(DeliveryRegion $deliveryRegion){
        $this->deliveryRegion = $deliveryRegion;
    }

    function getDeliveryRegion(){
        return $this->deliveryRegion or null;
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
     * Get the value of location
     */
    public function getLocation(): string | null
    {
        return $this->location;
    }

    /**
     * Set the value of location
     */
    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the value of mapsLocation
     */
    public function getMapsLocation(): string
    {
        return $this->mapsLocation;
    }

    /**
     * Set the value of mapsLocation
     */
    public function setMapsLocation(string $mapsLocation): self
    {
        $this->mapsLocation = $mapsLocation;

        return $this;
    }

    /**
     * Get the value of phoneNumber
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * Set the value of phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get the value of orderGroup
     */
    public function getOrderGroup()
    {
        return $this->orderGroup;
    }

    /**
     * Set the value of orderGroup
     */
    public function setOrderGroup($orderGroup): self
    {
        $this->orderGroup = $orderGroup;

        return $this;
    }



    /**
     * Get the value of postalCode
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * Set the value of postalCode
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get the value of defaultData
     */
    public function isDefaultData(): bool
    {
        return $this->defaultData;
    }

    /**
     * Set the value of defaultData
     */
    public function setDefaultData(bool $defaultData): self
    {
        $this->defaultData = $defaultData;

        return $this;
    }

    /**
     * Get the value of delivery
     */
    public function isDelivery(): bool
    {
        return $this->delivery;
    }

    /**
     * Set the value of delivery
     */
    public function setDelivery(bool $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }
}