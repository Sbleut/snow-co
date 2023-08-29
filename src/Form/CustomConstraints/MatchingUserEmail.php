<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Undocumented class
 */
class MatchingUserEmail extends Constraint
{
    public $message = 'The email does not match the user\'s email associated with the UUID.';
}