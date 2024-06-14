<?php

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;
//cap-code ,first-name ,family-name, password, email, phone-number, address

class SignInValidator extends AbstractValidator {
    function validate($data) {
        $v = new Validator($data);
        $v->rules([
            "required" => ["first-name" ,"family-name", "password", "email", "phone-number", "address"],
            "regex" => [
                ["first-name","/^[a-zA-Z0-9\p{Arabic}]+$/u"],
                ["family-name","/^[a-zA-Z0-9\p{Arabic}]+$/u"],
                ["password","/^[a-zA-Z0-9]+$/"],
                ["address","/^[a-zA-Z0-9\p{Arabic}\-]+$/u"],
            ],
            "numeric" => ["phone-number"],
            "email" => ["email"]
        ]);

        $this->checkValidation($v);
    }
}