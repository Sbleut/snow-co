<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

/**
 * MatchingUserEmailValidator
 */
class MatchingUserEmailValidator extends ConstraintValidator
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        $form = $this->context->getObject();
        $uuid = $form->getParent()->getConfig()->getOption('uuid');
        $token = $form->getParent()->getConfig()->getOption('token');

        // Retrieve the user based on the UUID
        $user = $this->entityManager->getRepository(User::class)->findOneByUuidToken($uuid->toBinary(), $token);

        if (!$user || $value !== $user->getEmail()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
