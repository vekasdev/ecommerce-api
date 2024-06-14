<?php

use App\model\CartService;
use App\model\DiscountCodeService;
use App\model\EntityServiceProvider;
use App\model\ImageService;


return [
    Image::class => ImageService::class,
    DiscountCode::class => DiscountCodeService::class
];