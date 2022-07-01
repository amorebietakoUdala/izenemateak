<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsValidDNI extends Constraint
{
    public $message = 'validation.invalidDNI';
}
