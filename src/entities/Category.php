<?php

use App\repositories\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass:CategoriesRepository::class)]
#[ORM\Table(name:"categories")]
class Category {

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private $id;

    #[ORM\Column(type:Types::STRING,unique:true)]
    private $categoryName;

    #[ORM\ManyToMany(targetEntity:Product::class,mappedBy:"categories")]
    private $products;

    #[ORM\ManyToMany(targetEntity:MainCategory::class,inversedBy:"categories",cascade:["persist"])]
    private $parentCategories;

    function __construct(){
        $this->products = new ArrayCollection();
        $this->parentCategories = new ArrayCollection();
    }

    function addParentCategory(MainCategory $category) {
        $this->parentCategories->add($category);
    }
    function getParentCategories() {
        return $this->parentCategories;
    }

    function getProducts() : ArrayCollection{
        return $this->products;
    }

    function addProduct(Product $product) {
        $this->products->add($product);
        $product->addCategory($this);
    }

    function removeProduct(Product $product) {
        $this->products->removeElement($product);
    }
    
    #[ORM\PreRemove]
    function preRemove() {
        foreach($this->products as $product) {
            $product->removeCategory($this);
        }
        $this->products->clear();
    }
    /**
     * Get the value of categoryName
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * Set the value of categoryName
     */
    public function setCategoryName($categoryName): self
    {
        $this->categoryName = $categoryName;

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
}