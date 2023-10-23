<?php 

namespace App\validators;
use App\model\AbstractValidator;
use Valitron\Validator;


class GetOrderGroupsValidator extends AbstractValidator {
    function validate($data) {
        $v = new Validator($data);

        $v->rules([
            "optional" => ["status"],
            "in" => [["status",["1","2","3"]]],
        ]);

        $this->checkValidation($v);
    }
}