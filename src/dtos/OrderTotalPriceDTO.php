<?php

namespace App\dtos;

class OrderTotalPriceDTO {
    function __construct(
        public bool $discounted,
        public float $currentPrice,
        public float | null  $previousPrice = null
    ){}
}