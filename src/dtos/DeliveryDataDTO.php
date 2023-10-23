<?php

namespace App\dtos;

use DeliveryRegion;

class DeliveryDataDTO {
    function __construct(
        public string $name,
        public string $phoneNumber,
        public string $location,
        public DeliveryRegion $region,
        public int $postalCode,
        public $mapsLocation     = [],
        public bool $defaultData = false,
        public bool $delivery    = false
    ){}
}
