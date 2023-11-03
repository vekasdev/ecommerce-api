<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\repositories\PromotionAdsRepository;

#[ORM\Entity(repositoryClass:PromotionAdsRepository::class)]
#[ORM\Table(name:"promotion_ads")]
class PromotionAd {

    function __construct() {
        $this->images = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    private $id;

    #[ORM\OneToMany(targetEntity:Image::class, mappedBy:"promotionAd",cascade:["persist","remove"])]
    #[ORM\JoinColumn(name:"image_id",referencedColumnName:"id",nullable:true)]
    private  $images;

    
    #[ORM\Column(type:Types::BOOLEAN)]
    private bool $main;



    public function getId()
    {
        return $this->id;
    }


    public function getImage()
    {
        return $this->images;
    }


    public function addImage(Image $image): self
    {
        $image->setPromotionAd($this);
        $this->images->add($image);
        return $this;
    }

    /**
     * Get the value of main
     */
    public function isMain(): bool
    {
        return $this->main;
    }

    /**
     * Set the value of main
     */
    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }

    function getImages() {
        return $this->images;
    }

}