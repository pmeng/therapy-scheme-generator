<?php

namespace App\Form;

use App\Entity\Therapy\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TherapySchemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Title',
            ])
            ->add('objective', TextareaType::class, [
                'required' => false,
                'label' => 'Objective',
            ])
            ->add('place', TextType::class, [
                'required' => false,
                'label' => 'Place',
            ])
            ->add('date', DateType::class, [
                'required' => false,
                'label' => 'Date',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('salutation', TextAreaType::class, [
                'required' => false,
                'label' => 'Salutation',
            ])
            ->add('footer', TextType::class, [
                'required' => false,
                'label' => 'Footer',
            ])
            ->add('labels', EntityType::class, [
                'class' => Label::class,
                'choice_label' => 'shortName',
                'multiple' => true,
                'mapped' => false,
            ])
            ->add('selectAll', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'data' => true
            ])
            // ->add('suppress', CheckboxType::class, [
            //     'required' => false,
            //     'label' => false
            // ])
            // ->add('excerpt', CheckboxType::class, [
            //     'required' => false,
            //     'label' => false
            // ])
            ->add('comments', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['style' => 'display:none', 'readonly' => true]
            ])
            ->add('targets', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['style' => 'display:none', 'readonly' => true]
            ])
            ->add('stubsOrder', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['style' => 'display:none', 'readonly' => true]
            ])->add('freeText', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['style' => 'display:none', 'readonly' => true]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
