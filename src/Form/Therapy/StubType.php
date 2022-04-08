<?php

namespace App\Form\Therapy;

use App\Entity\Therapy\Label;
use App\Entity\Therapy\Stub;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'therapy_stub_form_name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'therapy_stub_form_description',
            ])
            ->add('background', TextareaType::class, [
                'label' => 'therapy_stub_form_background',
            ])
            ->add('labels', EntityType::class, [
                'label' => 'therapy_stub_form_labels',
                'attr' => ['class' => 'select2 form-control select2-widget'],
                'row_attr' => ['class' => 'form-group'],
                'multiple' => true,
                'class' => Label::class,
            ])
            ->add('cancel', ButtonType::class, [
                'label' => 'form_cancel',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form_save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stub::class,
        ]);
    }
}
