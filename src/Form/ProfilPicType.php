<?php

namespace App\Form;

use App\Entity\ProfilPic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilPicType extends AbstractType
{
    // public function buildForm(FormBuilderInterface $builder, array $options): void
    // {
    //     $builder
    //         ->add('fileName')
    //         ->add('uuid')
    //     ;
    // }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => ProfilPic::class,
            'choice_label' => 'name', // Adjust this to match your entity property
            'multiple' => false,      // Allow only one selection
        ]);
    }
}
