<?php

use App\validators\GetProductsValidator;
use App\validators\AddOrderValidator;
use App\validators\AddProductValidator;
use App\validators\GetOrderGroupsValidator;
use App\validators\SetDeliveryDataValidator;

return [
    GetProductsValidator::class => new GetProductsValidator,
    AddOrderValidator::class => new AddOrderValidator,
    SetDeliveryDataValidator::class => new SetDeliveryDataValidator,
    GetOrderGroupsValidator::class => new GetOrderGroupsValidator,
    AddProductValidator::class => new AddProductValidator
];