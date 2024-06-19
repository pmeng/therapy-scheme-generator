<?php

namespace App\Form;

use App\Entity\Therapy\SchemeSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchemeSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fontChoices = [
            'Sans-Serif' => 'sans-serif',
            'Times' => 'times',
            'Times-Roman' => 'times-roman',
            'Courier' => 'courier',
            'Helvetica' => 'helvetica',
            'Zapfdingbats' => 'zapfdingbats',
            'Monospace' => 'monospace',
            'Symbol' => 'symbol',
            'Serif' => 'serif',
            'Fixed' => 'fixed',
            'DejaVu Sans' => 'dejavu sans',
            'DejaVu Sans Mono' => 'dejavu sans mono',
            'DejaVu Serif' => 'dejavu serif',
        ];

        $builder
            ->add('logo', FileType::class, [
                'required' => false,
                'data_class' => null,
                'mapped' => false
            ])
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Title'
            ])
            ->add('objective', TextareaType::class, [
                'required' => false,
                'label' => 'Objective'
            ])
            ->add('place', TextType::class, [
                'required' => false,
                'label' => 'Place',
            ])
            ->add('salutation', TextAreaType::class, [
                'required' => false,
                'label' => 'Salutation',
            ])
            ->add('footer', TextType::class, [
                'required' => false,
                'label' => 'Footer',
            ])
            ->add('textFontStyle', ChoiceType::class, [
                'choices' => $fontChoices,
                'required' => false,
                'label' => 'Text Font Style',
            ])
            ->add('titleFontStyle', ChoiceType::class, [
                'choices' => $fontChoices,
                'required' => false,
                'label' => 'Title Font Style',
            ])
            ->add('headingFontStyle', ChoiceType::class, [
                'choices' => $fontChoices,
                'required' => false,
                'label' => 'Heading Font Style',
            ])
            ->add('textFontSize', IntegerType::class, [
                'required' => false,
                'label' => 'Text Font Size',
            ])
            ->add('titleFontSize', IntegerType::class, [
                'required' => false,
                'label' => 'Title Font Size',
            ])
            ->add('headingFontSize', IntegerType::class, [
                'required' => false,
                'label' => 'Heading Font Size',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SchemeSetting::class,
        ]);
    }
}
