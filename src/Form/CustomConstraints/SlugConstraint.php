<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class SlugExist extends Constraint
{
    public $message = 'The name "{{ value }}" already exist.';
}
