<?php

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\repositories\UsersRepository;

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

    #[ORM\OneToMany(targetEntity:OrderGroup::class,mappedBy:"user")]
    private $orderGroups;

    #[ORM\Column(type:Types::BOOLEAN)]
    private  bool $admin = false ;
    
    function __construct() {
        $this->orderGroups = new ArrayCollection();
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
}