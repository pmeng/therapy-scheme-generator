<?php

namespace App\Form;

use App\Entity\Therapy\Scheme;
use App\Form\DataTransformer\ArrayToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTherapySchemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Title',
            ])
            ->add('objective', TextType::class, [
                'required' => false,
                'label' => 'Objective',
            ])
            ->add('place', TextType::class, [
                'required' => false,
                'label' => 'Place',
            ])
            ->add('schemeDate', DateType::class, [
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
            ->add('selectAll', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'data' => false,
                'mapped' => false
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
                'attr' => ['style' => 'display:none', 'readonly' => true],
            ])
            ->add("updateCurrent", CheckboxType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['style' => 'display:none']
            ])->add('freeText', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['style' => 'display:none', 'readonly' => true],

            ]);
            

        $builder
            ->get('comments')
            ->addModelTransformer(new ArrayToStringTransformer());

        $builder
            ->get('targets')
            ->addModelTransformer(new ArrayToStringTransformer());
            
        $builder
            ->get('stubsOrder')
            ->addModelTransformer(new ArrayToStringTransformer());

        $builder
            ->get('freeText')
            ->addModelTransformer(new ArrayToStringTransformer());
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "data_class" => Scheme::class,
        ]);
    }
}
