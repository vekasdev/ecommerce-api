<?php namespace App\dtos;

class DeliveryRegionDataDTO   {
    function __construct(
        public string | null $region = null,
        public bool | null $available = null,
        public float | null $cost = null
    ) {}
}