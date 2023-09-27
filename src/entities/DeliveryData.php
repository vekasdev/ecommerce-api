<?php

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[Entity()]
class DeliveryData {

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;
    #[ORM\Column(type:types::STRING)]
    private string $location;
    #[ORM\Column(type:types::STRING)]
    private string $mapsLocation;
    #[ORM\Column(type:types::STRING)]
    private string $city;
    #[ORM\Column(type:types::STRING)]
    private string $phoneNumber;

    #[ORM\ManyToOne(targetEntity:DeliveryRegion::class,inversedBy:"deliveryData",cascade:["persist","remove"])]
    private $deliveryRegion;

    #[ORM\OneToOne(targetEntity:OrderGroup::class,inversedBy:"deliveryData")]
    private $orderGroup;

    function getDeliveryCost() {
        return $this->deliveryRegion->getDeliveryCost();
    }

    function setDeliveryRegion(DeliveryRegion $deliveryRegion){
        $this->deliveryRegion = $deliveryRegion;
    }

    function getDeliveryRegion(){
        return $this->deliveryRegion;
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
    public function getLocation(): string
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
     * Get the value of city
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * Set the value of city
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

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
}