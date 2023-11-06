<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Trick;

class SlugExistValidator extends ConstraintValidator
{
    private $entityManager;
    private $slugger;

    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        // Check if email exists in the database
        $trick = $this->entityManager->getRepository(Trick::class)->findOneBy(['slug' => $this->slugger->slug($value)]);

        if (!$trick) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}