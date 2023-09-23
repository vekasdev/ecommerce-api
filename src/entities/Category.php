<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
class Category {

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type:Types::STRING)]
    private $categoryName;


    #[ORM\ManyToMany(targetEntity:Product::class,mappedBy:"categories")]
    private $products;

    function __construct(){
        $this->products = new ArrayCollection();
    }

    function addProduct(Product $product) {
        $this->products->add($product);
        $product->addCategory($this);
    }
}