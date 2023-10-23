<?php

namespace App\dtos;

use App\exceptions\RequestValidatorException;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Serializable;

class ResponseDataTransfer {
    function __construct(
        public ResponseInterface $res,
        public int $statusCode,
        public array | JsonSerializable $data = []
    ){}
}