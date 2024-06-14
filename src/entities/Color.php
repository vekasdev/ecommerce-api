<?php

use App\repositories\ColorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\OneToMany;

#[ORM\Entity(repositoryClass:ColorsRepository::class)]
#[ORM\Table(name:"colors")]
class Color {
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: Types::STRING,unique:true)]
    private string $colorName;

    #[ORM\Column(type: Types::STRING)]
    private string $colorHexCode;

    #[ORM\ManyToMany(targetEntity:Product::class,inversedBy:"colors",cascade:["persist"])]
    /** @var Product[] */
    private $products;


    #[ORM\OneToMany(targetEntity:Order::class ,mappedBy:"color",cascade:["persist"])]
    private $orders;

    function __construct(){
        $this->products = new ArrayCollection;
        $this->orders = new ArrayCollection;
    }

    function addOrder(Order $order) {
        $order->setColor($this);
        $this->orders->add($order);
    }

    function &getOrders() {
        return $this->orders;
    }
    #[ORM\PreRemove]
    function preRemove() {
        foreach($this->products as $product) {
            $product->getColors()->removeElement($this);
        }
    }

    function removeProduct(Product $product){
        $this->products->removeElement($product);
    }
    function addProduct(Product $product){
        $this->products->add($product);
    }

    function getProducts(){
        return $this->products;
    }

    
    /**
     * Get the value of colorHexCode
     */
    public function getColorHexCode(): string
    {
        return $this->colorHexCode;
    }

    /**
     * Set the value of colorHexCode
     */
    public function setColorHexCode(string $colorHexCode): self
    {
        $this->colorHexCode = $colorHexCode;

        return $this;
    }

    /**
     * Get the value of colorName
     */
    public function getColorName(): string
    {
        return $this->colorName;
    }

    /**
     * Set the value of colorName
     */
    public function setColorName(string $colorName): self
    {
        $this->colorName = $colorName;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }
}