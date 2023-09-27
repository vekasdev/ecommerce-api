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


    #[ORM\ManyToOne(targetEntity:Product::class,inversedBy:"images",cascade:["persist","remove"])]
    private Product $product;

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
}