<?php


namespace App\dtos;

use Category;
use Color;

class ProductDataFiltering {

    /**
     */
    function __construct(
        public null | string $name          = null,
        public null | int    $minPrice      = null,
        public null | int    $maxPrice      = null,
        public null | string $description   = null,
        public null | string $minStockQuantity = null,
        public null | int $category         = null,
        public float | null $productDiscount= null,
        public null | int $color            = null,
        public int $limit                   = 10,
        public int $fromIndex               = 1,
    ){}
}