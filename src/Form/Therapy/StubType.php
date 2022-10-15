<?php

namespace App\Form\Therapy;


use App\Entity\Therapy\Label;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StubType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'app-therapy-stub-form-label-name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'app-therapy-stub-form-label-description',
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'app-therapy-stub-form-label-excerpt',
            ])
            ->add('background', TextareaType::class, [
                'label' => 'app-therapy-stub-form-label-background',
            ])
            ->add('labels', ChoiceType::class, [
                'label' => 'app-therapy-stub-form-label-labels',
                'attr' => ['class' => 'select2 form-control select2-widget'],
                'row_attr' => ['class' => 'form-group'],
                'multiple' => true,
                'choices' => $this->entityManager->getRepository(Label::class)->findAll(),
                'choice_label' => function ($choice, $key, $value) {
                    return $value;
                },
                'choice_value' => function ($choice) {
                    return $choice;
                }
            ])
            ->add('save', SubmitType::class, [
                'label' => 'app-save-button',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $stub = $event->getData();
                $form = $event->getForm();

                $options = $form->get('labels')->getConfig()->getOptions();
                $options['choices'] = $stub['labels'];
                $form->add('labels', ChoiceType::class, $options);
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $stub = $event->getData();
                $form = $event->getForm();
                
                if (isset($stub->id)) {
                    $form->add('delete_undo', SubmitType::class, [
                        'label' => 'app-delete-undo-button'
                    ]);
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Stub::class,
        ]);
    }
}
