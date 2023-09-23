<?php

namespace App\dtos;

class EntryPersisted {
    function __construct(
        public bool $successed,
        public array $data
    ){}
}