<?php

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;

class AddOrderValidator extends AbstractValidator{ 
    function validate($data) {
        $v = new Validator($data);
        $v->rules([
            "required" => ["product","quantity"],
            "numeric" => ["product","quantity"]
        ]);

        $this->checkValidation($v);

    }
}