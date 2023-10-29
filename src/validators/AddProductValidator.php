<?php


namespace App\validators;

use App\model\AbstractValidator;
use Valitron\Validator;

class AddProductValidator extends AbstractValidator {
    function validate($data) {
        $v = new Validator($data);

        $v->rules([
            "required"=> [
                "name",
                "price",
                "description",
                "stock-qty",
                "categories",
                "colors",
                "discount-precentage"
            ],

            "regex" => [
                ["name","/^[a-zA-Z0-9\s\p{Arabic}]+$/u"],
                ["description","/^[a-zA-Z0-9\s\p{Arabic}]+$/u"],
                ["categories","/^\d+[,\d]*$/"],
                ["colors","/^\d+[,\d]*$/"]
            ],
            "numeric" => ["price","stock-qty","discount-precentage"],
        ]);

        $this->checkValidation($v);
    }
}


// $v->rules([
//     "required"=> [
//         "name",
//         "price",
//         "description",
//         "stock-qty",
//         "categories",
//         "images",
//         "colors",
//         "discount-precentage"
//     ],

//     "regex" => [
//         ["name","/^[a-zA-Z\s0-9\p{Arabic}]+$/u"],
//         ["description","/^[a-zA-Z\s0-9\p{Arabic}]+$/u"],
//         ["categories","/^\d+[,\d]*$/"],
//         ["images","/^\d+[,\d]*$/"],
//         ["colors","/^\d+[,\d]*$/"]
//     ],
//     "numeric" => ["price","stock-qty","discount-precentage"],
// ]);