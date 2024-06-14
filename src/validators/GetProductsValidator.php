<?php


namespace App\validators;

use App\model\AbstractValidator;
use Valitron\Validator;

class GetProductsValidator extends AbstractValidator {
    function validate($data){ 
        $v = new Validator($data);
        $v->rules([
            "optional" => [
                "name",
                "minPrice",
                "maxPrice",
                "description",
                "minStockQuantity",
                "category",
                "main-category",
                "color",
                "limit",
                "fromIndex",
                "pageCount",
                "min-product-discount"
            ],
            "min" => [
                ["pageCount",0],
                ['min-product-discount',0]
            ],
            "max"=>[
                ["pageCount",1],
                ['min-product-discount',1]
            ],
            "regex" => [
                ["name","/^[a-zA-Z0-9\s\p{Arabic}]+$/u"],
                ["description","/^[a-zA-Z\x{0600}-\x{06FF}]+$/u"],
            ],
            "numeric"=> ["main-category","pageCount","minPrice","maxPrice","minStockQuantity","category","min-product-discount","color","limit","fromIndex"],
        ]);

        $this->checkValidation($v);
    }
}
