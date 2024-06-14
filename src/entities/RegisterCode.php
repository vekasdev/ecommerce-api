<?php

use App\repositories\RegisterCodesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass:RegisterCodesRepository::class)]
class RegisterCode {
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type:Types::INTEGER)]
    private int $id;

    #[ORM\Column(type:Types::STRING)]
    private string $code;

    #[ORM\Column(type:Types::INTEGER)]
    private int $expire;

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
     * Get the value of expire
     */
    public function getExpire(): int
    {
        return $this->expire;
    }

    /**
     * Set the value of expire
     */
    public function setExpire(int $expire): self
    {
        $this->expire = $expire;

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