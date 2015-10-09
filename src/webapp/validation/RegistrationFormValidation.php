<?php

namespace tdt4237\webapp\validation;

use tdt4237\webapp\models\User;
use tdt4237\webapp\models\Email;

class RegistrationFormValidation
{
    const MIN_USER_LENGTH = 3;
    
    private $validationErrors = [];
    
    public function __construct($username, $password, $retype_pass, $fullname, $address, $postcode, $email)
    {
        return $this->validate($username, $password, $retype_pass, $fullname, $address, $postcode, $email);
    }
    
    public function isGoodToGo()
    {
        return empty($this->validationErrors);
    }
    
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    private function validate($username, $password, $retype_pass, $fullname, $address, $postcode, $email)
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

        if (preg_match('/^[A-Za-z0-9_]+$/', $username) === 0) 
        {
            $this->validationErrors[] = 'Username can only contain letters and numbers';
        }

        if (empty($password)) {
            $this->validationErrors[] = 'Password cannot be empty';
        }

        if( !($password === $retype_pass)){
            $this->validationErrors[] = 'The two passwords are not equal';
        }
    }
}
