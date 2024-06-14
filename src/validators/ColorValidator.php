<?php

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;

class ColorValidator extends AbstractValidator {
    function validate($data) {

        $v = new Validator($data);

        $v->rules([
            "required" => ["name","hex-code"],
            "regex" => [
                ["name","/^[a-z\-]+$/"],
                ["hex-code","/^[\#]{1}[a-zA-Z0-9]{6,8}$/"]
            ]
        ]);

        $this->checkValidation($v);
    }
}