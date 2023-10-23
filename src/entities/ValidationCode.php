<?php
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
#[ORM\Entity]
class ValidationCode {
    function __construct() {
    }
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private int  $id ;

    #[ORM\Column(type:Types::STRING)]
    private string $code;

    #[ORM\Column(type:Types::DATETIME_MUTABLE)]
    private DateTime $expire;

    #[ORM\ManyToOne(targetEntity:User::class,inversedBy:"codes",cascade:["persist","remove"])]
    private User $user;

    #[ORM\Column(type:Types::BOOLEAN)]
    private bool $valid = true;

    /**
     * Get the value of user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Set the value of user
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of expire
     */
    public function getExpire(): DateTime
    {
        return $this->expire;
    }

    /**
     * Set the value of expire
     */
    public function setExpire(DateTime $expire): self
    {
        $this->expire = $expire;

        return $this;
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
}