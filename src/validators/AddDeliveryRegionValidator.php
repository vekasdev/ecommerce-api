<?php

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;

class AddDeliveryRegionValidator extends AbstractValidator {
    function validate($data) {
        $v = new Validator($data);

        $v->rules([
            "required" => ["region","cost","available"],
            "regex" => [
                ["region" , "/^[a-zA-Z0-9\s\p{Arabic}\/\-]+$/u"],
                ["available" , "/^[0-1]{1}$/"]
            ],
            "numeric" => ["cost"]
        ]);

        $this->checkValidation($v);
    }
}