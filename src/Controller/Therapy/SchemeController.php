<?php

namespace App\Controller\Therapy;

use App\Entity\Therapy\Label;
use App\Form\Therapy\SchemeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SchemeController extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/new', name: 'app_therapy_scheme_new')]
    public function index(Request $request): Response
    {
        $labelsData = $this->entityManager->getRepository(Label::class)->findAll();

        $schemeForm = $this->createForm(SchemeType::class);
        $schemeForm->handleRequest($request);

        if ($schemeForm->isSubmitted() && $schemeForm->isValid()) {
            // TODO
        }

        return $this->render('therapy/scheme/index.html.twig', [
            'data' => $labelsData,
            'schemeForm' => $schemeForm->createView(),
        ]);
    }
}
