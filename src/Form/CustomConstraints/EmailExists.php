<?php 

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailExists extends Constraint
{
    public $message = 'The email "{{ value }}" does not exist.';
}