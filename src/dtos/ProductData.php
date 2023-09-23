<?php


namespace App\dtos;
class ProductData {
    function __construct(
        public string $name,
        public int $price,
        public string $description,
        public string $stockQuantity,
        public array $categories,
        public array $images
    ){}
}