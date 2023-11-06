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

    /**
     * Undocumented function
     *
     * @param [type] $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            return;
        }

        // Check if email exists in the database
        $trick = $this->entityManager->getRepository(Trick::class)->findOneBy(['slug' => $this->slugger->slug($value->getName())]);

        if ($trick) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->getName())
                ->atPath('form.name')
                ->addViolation();
        }
    }
}