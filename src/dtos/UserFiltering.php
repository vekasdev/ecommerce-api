<?php

namespace App\dtos;

class UserFiltering {
    function __construct(
        public string | null $email = null,
        public string | null $password = null,
        public string | null $id = null,
        public string | null $firstName = null,
        public string | null $lastName = null,
        public string | null $phoneNumber = null
    ){}
}