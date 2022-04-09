<?php

namespace App\Form\Therapy;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('selected', ChoiceType::class, [
                'attr' => ['class' => 'form-check-input'],
                'multiple' => true,
            ])
            ->add('comments', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'multiple' => true,
            ])
            ->add('generate', SubmitType::class, [
                'label' => 'Generate Report',
                'attr' => [
                    'class' => 'btn btn-primary flex-fill w-100',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
