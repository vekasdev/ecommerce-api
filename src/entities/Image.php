<?php

use App\repositories\ImageRespository;
use App\repositories\ImagesRepository;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;

#[Entity(repositoryClass:ImagesRepository::class)]
#[ORM\Table(name:"images")]
class Image {
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;

    #[Column(type:types::STRING)]
    private string $fileName;


    #[Column(type:types::STRING)]
    private string $extension;


    #[ORM\ManyToOne(targetEntity:Product::class,inversedBy:"images")]
    #[ORM\JoinColumn(name:"product_id",referencedColumnName:"id", nullable: true)]
    private Product | null $product;


    #[ORM\ManyToOne(targetEntity:PromotionAd::class,inversedBy:"images")]
    #[ORM\JoinColumn(name:"promotion_ad_id",referencedColumnName:"id", nullable: true)]
    private PromotionAd | null $promotionAd;

    #[ORM\ManyToOne(targetEntity:DiscountCode::class,inversedBy:"image")]
    #[ORM\JoinColumn(referencedColumnName:"id", nullable: true)]
    private $discountCode;

    function __construct(){
        
    }

    #[ORM\PreRemove]
    function preRemove() {
        if($this->promotionAd !== null) {
            $this->promotionAd->getImages()->removeElement($this);
        }
        if($this->product !== null) {
            $this->product->getImages()->removeElement($this);
        }
    }


    function setDiscountCode(DiscountCode | null $discountCode) {
        $this->discountCode = $discountCode;
    }

    function setPromotionAd(PromotionAd $promotionAd) {
        $this->promotionAd = $promotionAd;
    }

    #[ORM\PreRemove]
    function delete() {
        $this->product->getImages()->removeElement($this);
    }
    

    /** @return string filename.extention */
    function getFullFileName(){
        return $this->fileName.".".$this->extension;
    }

    function getMediaType(){
        return "image/".$this->extension;
    }

    function setProduct(Product $product) {
        $this->product = $product;
    }

    function getProduct() : Product {
        return $this->product;
    }
    
    /**
     * Get the value of fileName
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * Set the value of fileName
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get the value of extension
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * Set the value of extension
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId() : int
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
}