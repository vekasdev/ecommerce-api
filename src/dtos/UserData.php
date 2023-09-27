<?php

namespace App\dtos;


class UserData {
    function __construct(
        public string $firstName,
        public string $familyName,
        public string $password,
        public string $email,
        public string $phoneNumber,
        public string $address  
    ){}
}