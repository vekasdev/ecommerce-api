<?php


namespace App\dtos;
class ProductData {

    /**
     * @param \Color[] $colors
     * @param \Image[] $images
     * @param \Category[] $categories
     * 
     */
    function __construct(
        public string $name,
        public int $price,
        public string $description,
        public string $stockQuantity,
        public array $categories,
        public array $images = [],
        public float | null $productDiscount = null,
        public array | null $colors          = null
    ){}
}