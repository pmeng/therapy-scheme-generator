<?php

namespace App\Twig\Components;

use App\Entity\Therapy\Scheme;
use App\Form\SchemeTemplateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('scheme-template')]
final class SchemeTemplateComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(fieldName: 'name')]
    public ?Scheme $scheme = null;
    #[LiveProp(writable: true)]
    public array $targets;
    #[LiveProp(writable: true)]
    public array $comments;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SchemeTemplateType::class, $this->scheme);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        $scheme = $this->getFormInstance()->getData();
        $scheme->setTargets($this->targets);
        $scheme->setComments($this->comments);
        $scheme->setCreatedAt(new \DateTimeImmutable());
        $scheme->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($scheme);
        $entityManager->flush();
        
        $this->addFlash('success', 'Scheme template saved!');

        return $this->redirectToRoute('app_therapy_scheme_templates_list', [
            'id' => $scheme->getId(),
        ]);
    }
}
