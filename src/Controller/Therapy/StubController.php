<?php

namespace App\Controller\Therapy;

use App\Entity\Therapy\Stub;
use App\Form\Therapy\StubType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class StubController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/new', name: 'app_therapy_stub_new')]
    public function newStub(Request $request): Response
    {
        $stubForm = $this->createForm(StubType::class);
        $stubForm->handleRequest($request);

        if ($stubForm->isSubmitted() && $stubForm->isValid()) {
            // TODO
        }

        return $this->render('therapy/stub/index.html.twig', [
            'formTitle' => $this->translator->trans('stub_new_form_title'),
            'stubForm' => $stubForm->createView(),
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/edit/{id<\d+>}', name: 'app_therapy_stub_edit')]
    public function editStub(Request $request, int $id): Response
    {
        $stub = $this->entityManager->getRepository(Stub::class)->find($id);

        if (!$stub) {
            // TODO exception about stub not exists
        }

        $stubForm = $this->createForm(StubType::class, $stub);
        $stubForm->handleRequest($request);

        if ($stubForm->isSubmitted() && $stubForm->isValid()) {
            // TODO
        }

        return $this->render('therapy/stub/index.html.twig', [
            'formTitle' => $this->translator->trans('stub_edit_form_title', [
                'stub_name' => $stub->getName()
            ]),
            'stubForm' => $stubForm->createView(),
        ]);
    }
}
