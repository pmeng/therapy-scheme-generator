<?php

namespace App\Form\Therapy;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Repository\Therapy\LabelRepository;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StubCategoryType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shortName', TextType::class, [
                'label' => 'app-therapy-stub-category-form-label-short-name',
                'translation_domain' => 'messages',
            ])
            ->add('reportName', TextType::class, [
                'label' => 'app-therapy-stub-category-form-label-report-name',
                'translation_domain' => 'messages',
            ])
            ->add('categoryOrder', IntegerType::class, [
                'label' => 'app-therapy-stub-category-form-label-order',
                'translation_domain' => 'messages',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Add New Category',
                'translation_domain' => 'messages',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
