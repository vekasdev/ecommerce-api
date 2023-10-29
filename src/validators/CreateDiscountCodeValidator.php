<?php

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;

class CreateDiscountCodeValidator extends AbstractValidator {
    function validate($data) {
        $v =new Validator($data);
        $v->rules(
            [
                "required" => ["code","precentage","valid"],
                "regex" => [
                    ["code","/^[a-zA-Z0-9]+$/"],
                    ["valid","/^[0-1]{1}$/"]
                ],
                "min" => [["precentage",0]],
                "max" => [["precentage",100]]
            ]
        );
        $this->checkValidation($v);
    }
}