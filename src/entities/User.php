<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\repositories\UsersRepository;
use App\model\OrderGroupStatus;
#[ORM\Entity(repositoryClass:UsersRepository::class)]
#[ORM\Table(name:"users")]
class User {
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int  $id ;

    #[ORM\Column(type:Types::STRING)]
    private  string $firstName;

    #[ORM\Column(type:Types::STRING)]
    private string $familyName;

    #[ORM\Column(type:Types::STRING)]
    private  string $password;

    #[ORM\Column(type:Types::STRING,unique:true)]
    private string $email;

    #[ORM\Column(type:Types::STRING,length:15)]
    private $phoneNumber;
    #[ORM\Column(type:Types::STRING)]
    private $address;

    #[ORM\OneToMany(targetEntity:OrderGroup::class,mappedBy:"user",cascade:["persist"])]
    private $orderGroups;

    #[ORM\Column(type:Types::BOOLEAN)]
    private  bool $admin = false ;
    
    #[ORM\Column(type : Types::BOOLEAN)]
    private bool $valid = false ;

    #[ORM\OneToMany(targetEntity:Cart::class,mappedBy:"user")]
    private  $carts;

    #[ORM\OneToMany(targetEntity:ValidationCode::class,mappedBy:"user",cascade:["persist","remove"])]
    private $codes;

    #[ORM\OneToMany(targetEntity:DeliveryData::class,mappedBy:"user",cascade:["persist"])]
    private  $deliveryData ;
    
    #[ORM\ManyToMany(targetEntity:Product::class,mappedBy:"interestedUsers")]
    private $favorites;

    function __construct() {
        $this->orderGroups = new ArrayCollection();
        $this->codes = new ArrayCollection();
        $this->deliveryData = new ArrayCollection;
        $this->carts = new ArrayCollection;
        $this->favorites = new ArrayCollection();
    }

    function getFavorites() {
        return $this->favorites;
    }
    
    function addProductToFavorites(Product $product) {
        $this->favorites->add($product);
        $product->addInterestedUser($this);
    }

    function removeFavoriteProduct(Product $product) { 
        $this->favorites->removeElement($product);
        $product->getInterestedUsers()->removeElement($this);
    }

    function getOrderGroups() {
        return $this->orderGroups;
    }

    function getUnInitializedOrderGroup() : null | OrderGroup{
        foreach($this->orderGroups as $orderGroup) {
            $status = $orderGroup->getStatus();
            if($status == OrderGroupStatus::NOT_INITIALIZED) return $orderGroup;
        }
        return null;
    }

    function addDeliveryData(DeliveryData $deliveryData) {
        $deliveryData->setUser($this);
        $this->deliveryData->add($deliveryData);
    }

    function addCode(ValidationCode $validationCode) {
        if(count($this->codes) >= 1) {
            foreach($this->codes as $code) {
                $code->setValid(false);
            }
        }
        $this->codes->add($validationCode);
        $validationCode->setUser($this);
    }
    
    function setDefaultDeliveryData(DeliveryData $deliveryData) {
        if($this->deliveryData->contains($deliveryData)) {
            foreach($this->deliveryData as $_deliveryData) {
                $_deliveryData->setDefaultData(false);
                $deliveryData->setDefaultData(true);
            }
        }
    }

    function getDefaultDeliveryData() : null | DeliveryData{
        foreach($this->deliveryData as $_deliveryData) {
            if($_deliveryData->isDefaultData()) return $_deliveryData;
        };
        return null;
    }

    function getCode(string $code){
        /**
         * @var ValidationCode $codeObj
         */
        foreach($this->codes as $codeObj){
            if($codeObj->getCode() == $code) return $codeObj;
        }
        return null;
    }

    function addOrderGroup(OrderGroup $orderGroup){
        $this->orderGroups->add($orderGroup);
        $orderGroup->setUser($this);
    }


    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of firstName
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }



    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of phoneNumber
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the value of phoneNumber
     */
    public function setPhoneNumber($phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get the value of address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     */
    public function setAddress($address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of familyName
     */
    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    /**
     * Set the value of familyName
     */
    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of admin
     */
    public function isAdmin(): bool
    {
        return $this->admin;
    }

    /**
     * Set the value of admin
     */
    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

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
     * Get the value of cart
     */
    public function getNonProcessedCart() : null | Cart
    {
        foreach($this->carts as $cart) {
            if($cart->isProcessed() == false) 
                return $cart;
        }
        return null;
    }

    /**
     * Set the value of cart
     */
    public function addCart(Cart $cart): self
    {
        $cart->setUser($this);
        $this->carts->add($cart) ;

        return $this;
    }
}