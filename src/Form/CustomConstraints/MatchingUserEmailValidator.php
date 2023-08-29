<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;

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
        $user = $this->entityManager->getRepository(User::class)->findOneByUuid($uuid->toBinary());
    
        if (!$user || $value !== $user->getEmail() || isset($token)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
