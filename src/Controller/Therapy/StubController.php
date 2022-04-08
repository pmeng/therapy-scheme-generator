<?php

namespace App\Controller\Therapy;

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

    #[Route('/therapy/stub', name: 'app_therapy_stub')]
    public function index(): Response
    {
        return $this->render('therapy/stub/index.html.twig', [
            'controller_name' => 'StubController',
        ]);
    }
}
