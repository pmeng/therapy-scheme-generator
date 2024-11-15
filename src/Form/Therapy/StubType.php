<?php

namespace App\Form\Therapy;


use App\Entity\Therapy\Label;
use App\Entity\Therapy\StubCategory;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Repository\Therapy\LabelRepository;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class StubType extends AbstractType
{
    private LabelRepository $labelRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(LabelRepository $labelRepository, EntityManagerInterface $entityManager)
    {
        $this->labelRepository = $labelRepository;
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
                'attr' => [
                    'class' => 'quill-editor', 
                    'data-quill-target' => 'description', 
                ],
            ])
            ->add('excerpt', TextareaType::class, [
                'label' => 'app-therapy-stub-form-label-excerpt',
                'attr' => [
                    'class' => 'quill-editor',
                    'data-quill-target' => 'excerpt',
                ],
            ])
            ->add('background', TextareaType::class, [
                'label' => 'app-therapy-stub-form-label-background',
                'attr' => [
                    'class' => 'quill-editor',
                    'data-quill-target' => 'background',
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
            ->add('category', EntityType::class, [
                'label' => 'Category',
                'attr' => ['class' => 'select2 form-control select2-widget'],
                'class' => StubCategory::class,
                'choice_label' => 'shortName',
                'multiple' => false,
                'required' => true,
            ])
            ->add('submitAndNew', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['style' => 'display: none;'],
            ])->add('submitAndDuplicate', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => ['style' => 'display: none;'],
            ]);

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function(PreSubmitEvent $event){
                $data = $event->getData();
                
                $labels = $data['labels'] ?? [];

                for ($i = 0; $i < count($labels); $i++) {
                    if (!is_numeric($labels[$i])) {
                        $label = new Label();
                        $label->setShortName($labels[$i]);
                        $label->setReportName($labels[$i]);
                        $this->entityManager->persist($label);
                        $this->entityManager->flush();

                        $createdLabel = $this->labelRepository->findOneBy(['shortName' => $labels[$i]]);
                        $labels[$i] = $createdLabel->getId();
                    }
                }

                $data['labels'] = $labels;
                $event->setData($data);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Stub::class,
        ]);
    }
}
