<?php 

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;

class AddPromotionAdValidator extends AbstractValidator {
    function validate($data) {
        $v = new Validator($data);

        $v->rules([
            "required" => ["main"],
            "regex" => [
                ["main","/^[0-1]{1}$/"]
            ]
        ]);

        $this->checkValidation($v);
    }

}