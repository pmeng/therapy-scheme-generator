<?php

namespace App\Form\Therapy;


use App\Entity\Therapy\Label;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app-therapy-stub-form-label-name',
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'app-therapy-stub-form-label-description',
                'config' => [
                    'removePlugins' => 'print,preview,save,newpage,sourcearea,templates,exportpdf,pastefromword,scayt,forms,div,language,image,smiley,iframe,about,maximize,showblocks',
                ],
            ])
            ->add('excerpt', CKEditorType::class, [
                'label' => 'app-therapy-stub-form-label-excerpt',
                'config' => [
                    'removePlugins' => 'print,preview,save,newpage,sourcearea,templates,exportpdf,pastefromword,scayt,forms,div,language,image,smiley,iframe,about,maximize,showblocks',
                ],
            ])
            ->add('background', CKEditorType::class, [
                'label' => 'app-therapy-stub-form-label-background',
                'config' => [
                    'removePlugins' => 'print,preview,save,newpage,sourcearea,templates,exportpdf,pastefromword,scayt,forms,div,language,image,smiley,iframe,about,maximize,showblocks',
                ],
            ])
            ->add('labels', EntityType::class, [
                'label' => 'app-therapy-stub-form-label-labels',
                'attr' => ['class' => 'select2 form-control select2-widget'],
                'class' => Label::class,
                'choice_label' => 'shortName',
                'multiple' => true,
                'required' => true,
            ])
            ->add('submitAndNew', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['style' => 'display: none;'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Stub::class,
        ]);
    }
}
