<?php

namespace App\Validator\Password;

use Exception;

class PasswordStrengthException extends Exception
{
    public array $passwordErrors;





    public function __construct(array $passwordErrors)
    {
        $this->passwordErrors = $passwordErrors;

        parent::__construct("", 0, null);
    }
}
