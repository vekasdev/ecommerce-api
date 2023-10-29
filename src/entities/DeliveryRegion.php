<?php
use App\repositories\DeliveryRegionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass:DeliveryRegionRepository::class)]
class DeliveryRegion {
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::STRING,unique:true)]
    private string $name;

    #[ORM\Column(type: Types::DECIMAL)]
    private float $deliveryCost;

    #[ORM\OneToMany(targetEntity:DeliveryData::class,mappedBy:"deliveryRegion")]
    private $DeliveryData;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $available;

    function addDeliveryData(DeliveryData $deliveryData){
        $this->DeliveryData->add($deliveryData);
        $deliveryData->setDeliveryRegion($this);
    }


    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of deliveryCost
     */
    public function getDeliveryCost(): float
    {
        return $this->deliveryCost;
    }

    /**
     * Set the value of deliveryCost
     */
    public function setDeliveryCost(float $deliveryCost): self
    {
        $this->deliveryCost = $deliveryCost;

        return $this;
    }

    /**
     * Get the value of available
     */
    public function isAvailable(): bool
    {
        return $this->available;
    }

    /**
     * Set the value of available
     */
    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }
}