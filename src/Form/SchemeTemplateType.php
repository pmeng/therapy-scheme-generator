<?php

namespace App\Form;

use App\Entity\Therapy\Scheme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchemeTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app-scheme-template-form-label-name',
                'attr' => ['class' => 'form-control'],
            ])
            /*->add('targets', CollectionType::class, [
                'by_reference' => false,
            ])
            ->add('comments', CollectionType::class, [
                'by_reference' => false,
            ])
            ->add('createdAt', DateType::class)
            ->add('updatedAt', DateType::class)*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scheme::class,
        ]);
    }
}
