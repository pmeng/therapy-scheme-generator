<?php

namespace App\Controller\Therapy;


use App\Entity\Therapy\Label;
use App\Entity\Therapy\Scheme;
use App\Form\SchemeTemplateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;

class SchemeController extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/create', name: 'app_therapy_scheme_create')]
    public function create(Request $request): Response
    {
        return $this->render('therapy/scheme/create.html.twig');
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/load/{id}', name: 'app_therapy_scheme_load')]
    public function load(Request $request, int $id): Response
    {
        $scheme = $this->entityManager->getRepository(Scheme::class)->find($id);
        
        return $this->render('therapy/scheme/load.html.twig', [
            'template' => $scheme,
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/save-as-template', name: 'app_therapy_scheme_save_as_template')]
    public function saveAsTemplate(Request $request): Response
    {
        $data = $request->request->all();

        $scheme = new Scheme();
        $scheme->setName('Date: ' . date(DATE_RSS));

        $form = $this->createForm(SchemeTemplateType::class, $scheme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_therapy_scheme_templates_list');
        }

        return $this->render('therapy/scheme/save-as-template.html.twig', [
            'scheme' => $scheme,
            'targets' => $data['targets'] ?? [],
            'comments' => $data['comments'] ?? [],
            'form' => $form->createView()
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/new', name: 'app_therapy_scheme_new')]
    public function index(Request $request): Response
    {
        $data = $request->request->all();

        $labelsData = $allLabels = $this->entityManager->getRepository(Label::class)->findAll();
        if (isset($data['labels'])) {
            $qb = $this->entityManager->createQueryBuilder('l');
            $labelsData = $qb->select('lbl')
                ->where(
                    $qb->expr()->in(
                        'lbl.id', 
                        $data['labels']
                    )
                )
                ->from(Label::class, 'lbl')
                ->getQuery()
                ->getResult();
        }
        
        return $this->render('therapy/scheme/index.html.twig', [
            'all' => $allLabels,
            'data' => $labelsData,
            'selected' => isset($data['labels']) ? array_map('intval', $data['labels']) : [],
        ]);
    }
    
    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/generate/html', name: 'app_therapy_scheme_generate_html', methods: ['POST'])]
    public function generateHtml(Request $request): Response
    {
        $labelsData = $this->entityManager->getRepository(Label::class)->findAll();
        $data = $request->request->all();

        return $this->render('therapy/scheme/html-template.html.twig', [
            'data' => $labelsData,
            'targets' => $data['targets'],
            'comments' => $data['comments'],
            'suppress_labels' => isset($data['suppress_labels']) ? $data['suppress_labels'] : false,
            'use_excerpt' => isset($data['use_excerpt']) ? $data['use_excerpt'] : false,
        ]);
    }
    
    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/generate/pdf', name: 'app_therapy_scheme_generate_pdf', methods: ['POST'])]
    public function generatePdf(Request $request, Pdf $pdfGenerator): PdfResponse
    {
        $labelsData = $this->entityManager->getRepository(Label::class)->findAll();
        $data = $request->request->all();

        $html = $this->renderView('therapy/scheme/pdf-template.html.twig', [
            'data' => $labelsData,
            'targets' => $data['targets'],
            'comments' => $data['comments'],
            'suppress_labels' => isset($data['suppress_labels']) ? $data['suppress_labels'] : false,
            'use_excerpt' => isset($data['use_excerpt']) ? $data['use_excerpt'] : false,
        ]);
        
        return new PdfResponse(
            $pdfGenerator->getOutputFromHtml($html),
            sprintf('report-%s.pdf', date('Y-m-d'))
        );
    }
}
