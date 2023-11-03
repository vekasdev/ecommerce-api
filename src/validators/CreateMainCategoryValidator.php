<?php

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;

class CreateMainCategoryValidator extends AbstractValidator {
    public function validate($data) {
        $v = new Validator($data);
        $v->rules([
            "required" => ["name","categories"],
            "regex" => [
                ["categories","/^\d(,\d)*$/"]
            ]
        ]);

        $this->checkValidation($v);
    }
}