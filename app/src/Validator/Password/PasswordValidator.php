<?php

namespace App\Validator\Password;

class PasswordValidator
{
    private array $passwordErrors;





    public function validate(string $password)
    {
        // Check if Password contains a number
        if (!preg_match("/\d/", $password)) {
            $this->addPasswordError("Password must contain a number");
        }

        // Check if Password contains a lower case character
        if (!preg_match("/[a-z]/", $password)) {
            $this->addPasswordError("Password must contain a lower case letter");
        }

        // Check if Password contains an upper case character
        if (!preg_match("/[A-Z]/", $password)) {
            $this->addPasswordError("Password must contain an upper case letter");
        }

        if (!empty($this->passwordErrors)) {
            throw (new PasswordStrengthException($this->passwordErrors));
        }
    }




    
    public function addPasswordError(string $error): PasswordValidator
    {
        $this->passwordErrors[] = $error;

        return $this;
    }
}
