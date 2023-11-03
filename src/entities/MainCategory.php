<?php

use App\repositories\MainCategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass:MainCategoriesRepository::class)]
class MainCategory {
    #[ORM\Id,ORM\GeneratedValue(strategy:"IDENTITY"), ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING,unique: true)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy:"parentCategories",cascade:["persist"])]
    private $categories;
    

    function __construct() {
        $this->categories = new ArrayCollection();
    }
    

    #[ORM\PreRemove]
    function preRemove() {
        /**
         * @var Category $category
         */
        foreach($this->categories as $category) {
            $category->getParentCategories()->removeElement($this);
        }
        $this->categories->clear();
    }

    function getCategories() {
        return $this->categories;
    }
    function addCategory(Category $category) {
        $category->addParentCategory($this);
        $this->categories->add($category);
    }

    function clearCategories() {
        /** @var Category $category */
        foreach($this->categories as $category) {
            $category->getParentCategories()->removeElement($this);
            $this->categories->removeElement($category);
        }
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
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }
}