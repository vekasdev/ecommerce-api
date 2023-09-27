<?php

namespace App\dtos;

use Psr\Http\Message\ResponseInterface;

class EntryPersisted {
    function __construct(
        public ResponseInterface $responseInterface,
        public bool $successed,
        public array $data
    ){}
}