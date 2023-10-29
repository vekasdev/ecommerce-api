<?php 

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;

class FilteringDeliveryRegionValidator extends AbstractValidator {
    function validate($data) {
        $v = new Validator($data);

        $v->rules([
            "optional"=> ["region","cost","available"],
            "regex" => [
                ["region" , "/^[a-zA-Z0-9\s\p{Arabic}\/\-]+$/u"],
                ["available" , "/^[0-1]{1}$/"]
            ],
            "numeric" => ["cost"]
        ]);

        return $this->checkValidation($v);
    }
}