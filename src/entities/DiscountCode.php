<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\repositories\DiscountCodeRepository;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass:DiscountCodeRepository::class)]
class DiscountCode {
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type:Types::DECIMAL,precision:2, scale:2)]
    private float $precentage;

    #[ORM\Column(type:Types::STRING,unique:true)]
    private string $code;

    #[ORM\Column(type:Types::BOOLEAN)]
    private bool $valid;

    #[ORM\OneToMany(targetEntity:OrderGroup::class,mappedBy:"discountCode")]
    private $orderGroups;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $promoted;

    #[ORM\OneToMany(targetEntity:Image::class,mappedBy:"discountCode",cascade:["persist","remove"])]
    private $image;

    function __construct() {
        $this->orderGroups = new ArrayCollection();
        $this->image = new ArrayCollection();
    }

    function setImage(Image $image) {
        $this->clearImages();
        $this->image->clear();
        $this->image->add($image);
        $image->setDiscountCode($this);
    }

    function clearImages() {
        /** @var Image */
        foreach($this->image as $image) {
            $image->setDiscountCode(null);
        }
    }

    /**
     * @return Image | null
     */
    function getImage() {
        /** @var Image | bool  */
        $image = $this->image->first();
        return !is_bool($image) ?  $image : null;
    }

    function addOrderGroup(OrderGroup $orderGroup){
        $this->orderGroups->add($orderGroup);
    }

    function getOrderGroups() {
        return $this->orderGroups;
    }

    /**
     * Get the value of code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set the value of code
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the value of amount
     */
    public function getPrecentage(): float
    {
        return $this->precentage;
    }

    /**
     * Set the value of amount
     */
    public function setPrecentage(float $precentage): self
    {
        $this->precentage = $precentage;

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
     * Get the value of valid
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * Set the value of valid
     */
    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get the value of promoted
     */
    public function isPromoted(): bool
    {
        return $this->promoted;
    }

    /**
     * Set the value of promoted
     */
    public function setPromoted(bool $promoted): self
    {
        $this->promoted = $promoted;

        return $this;
    }
}