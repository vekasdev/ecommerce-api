<?php


namespace App\validators;

use App\model\AbstractValidator;
use Valitron\Validator;

class  SetDeliveryDataValidator extends AbstractValidator {
    function validate($data) { 
        $v = new Validator($data);

        $v->rules([
            "optional" => ["maps-location","defaultData","delivery"],
            "required" => [
                "name",
                "phone-number",
                "location",
                "region",
                "postal-code"
            ],
            "regex" => [
                ["maps-location","/^\d\,\d$/"],
                ["location","/^[\p{Arabic}\p{Latin}\d\s-]+$/u"],
                ["name","/^[\p{Arabic}\p{Latin}\d\s]+$/u"]
            ],
            "numeric"=>["phone-number","region","postal-code"],
            "min" => [["phone-number",11],["defaultData",0],["delivery",0]],
            "max" => [["defaultData",1],["delivery",1]]
        ]);

        $this->checkValidation($v);
    }
}



// public string $name,
// public string $phoneNumber,
// public string $location,
// public DeliveryRegion $region,
// public int $postalCode,
// public $mapsLocation = []