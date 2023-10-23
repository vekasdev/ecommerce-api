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
                "productDiscount",
                "color",
                "limit",
                "fromIndex",
                "pageCount"
            ],
            "min" => [
                ["pageCount",0]
            ],
            "max"=>[
                ["pageCount",1]
            ],
            "regex" => [
                ["name","/^[a-zA-Z\x{0600}-\x{06FF}]+$/u"],
                ["description","/^[a-zA-Z\x{0600}-\x{06FF}]+$/u"],
            ],
            "numeric"=> ["pageCount","minPrice","maxPrice","minStockQuantity","category","productDiscount","color","limit","fromIndex"],
        ]);

        $this->checkValidation($v);
    }
}
