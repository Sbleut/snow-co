<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SlugExist extends Constraint
{
    public $message = 'Le nom "{{ value }}" existe déjà.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

}
