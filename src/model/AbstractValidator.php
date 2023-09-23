<?php 


namespace App\model;
use App\exceptions\RequestValidatorException;
use App\interfaces\RequestValidatorInterface;

abstract class AbstractValidator implements RequestValidatorInterface {
    /**
     * @throws RequestValidatorException JSON serializable with errors and its explaination
     */
    function checkValidation($validator){
        if(!$validator->validate()) {
            $this->throwValidationException($validator->errors());
        }
        return true;
    }
    private function throwValidationException(array $errors){
        throw new RequestValidatorException($errors);
    }
    
    protected function registerAdditionalRules($v){
        $v->addRule("noSpecialChars",function($field, $value, array $params, array $fields){
            $illegal = "#$%^&*()+=-[]';,./{}|:<>?~";
            return !strpbrk($value,$illegal);
        });
    }
}