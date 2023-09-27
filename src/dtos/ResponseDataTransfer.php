<?php

namespace App\dtos;

use Psr\Http\Message\ResponseInterface;

class ResponseDataTransfer {
    function __construct(
        public ResponseInterface $res,
        public int $statusCode,
        public array $data
    ){}
}