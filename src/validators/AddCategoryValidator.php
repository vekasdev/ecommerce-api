<?php

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;

class AddCategoryValidator extends AbstractValidator {
    function validate($data) {
        $v = new Validator($data);
        $v->rules([
            "required" => ["name"],
            "regex" => [
                ["name","/^[a-zA-Z0-9\s\p{Arabic}\/\-]+$/u"]
            ]
        ]);
    }
}