<?php

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name:"users")]
class User {
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    protected int  $id ;
    #[ORM\Column(type:Types::STRING,length:150)]
    protected $userName;

    #[ORM\Column(type:Types::STRING,length:200)]
    protected $password;

    #[ORM\Column(type:Types::STRING,length:15)]
    protected $phoneNumber;
    #[ORM\Column(type:Types::STRING,length:200)]
    protected $address;
}