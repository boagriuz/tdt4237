<?php

namespace tdt4237\webapp\validation;

class EditUserFormValidation
{
    private $validationErrors = [];
    
    public function __construct($fullname, $address, $postcode, $email, $bio, $age)
    {
        $this->validate($fullname, $address, $postcode, $email, $bio, $age);
    }
    
    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }
    
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($fullname, $address, $postcode, $email, $bio, $age)
    {
        

        if(empty($fullname)) {
            $this->validationErrors[] = "Please write in your full name";
        }

        if(empty($address)) {
            $this->validationErrors[] = "Please write in your address";
        }

        if(empty($postcode)) {
            $this->validationErrors[] = "Please write in your post code";
        }

        if (strlen($postcode) != "4") {
            $this->validationErrors[] = "Post code must be exactly four digits";
        }

        //set email, validation is done in Email.php
        
        if(empty($email)){
            $this->validationErrors[] = "Please fill in your email";

        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->validationErrors[] = "Invalid email format on email";
        }

        if (! is_numeric($age) or $age < 0 or $age > 130) {
            $this->validationErrors[] = 'Age must be between 0 and 130.';
        }

        if (empty($bio)) {
            $this->validationErrors[] = 'Bio cannot be empty';
        }

    }
    
}
