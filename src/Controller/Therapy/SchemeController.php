<?php

namespace App\Controller\Therapy;

use App\Entity\Template;
use App\Entity\Therapy\Label;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
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

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/new/{template}', name: 'app_therapy_scheme_new')]
    public function index(Request $request, $template = null): Response
    {
        $data = $request->request->all();
        $allLabels = $this->entityManager->getRepository(Label::class)->findAll();
        $labelsData = [];
        $targets = [];

        if (!is_null($template)) {
            $templateObj = $this->entityManager->getRepository(Template::class)->findOneBy([
                'id' => $template,
            ]);
            $targets = $templateObj->getTargets();

            if (isset($data['labels'])) {
                $data['labels'] = array_merge($data['labels'], array_keys($targets));
            } else {
                $data['labels'] = array_keys($targets);
            }
        }

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
            'targets' => $targets,
            'template' => $template,
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
        ]);
        
        return new PdfResponse(
            $pdfGenerator->getOutputFromHtml($html),
            sprintf('report-%s.pdf', date('Y-m-d'))
        );
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/save/template/{templateId}', name: 'app_therapy_scheme_save_template', methods: ['POST'])]
    public function saveAsTemplate(Request $request, $templateId = null): Response
    {
        $data = $request->request->all();
        $targets = [];
        $comments = $data['comments'];
        $templates = [];

        foreach ($data['targets'] as $label => $stubs) {
            $targets[$label] = [];
            foreach (array_keys($stubs) as $key => $id) {
                $targets[$label][$id] = trim($comments[$id]);
            }
        }

        $name = date('Y-m-d H:i:s');

        if (is_null($templateId)) {
            $template = new Template();
        } else {
            $template = $this->entityManager->getRepository(Template::class)->findOneBy([
                'id' => $templateId,
            ]);
        }
        $template->setName($name);
        $template->setTargets($targets);
        $this->entityManager->persist($template);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_therapy_saved_templates');
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/templates/list', name: 'app_therapy_saved_templates')]
    public function loadTemplates(Request $request): Response
    {
        $templates = $this->entityManager->getRepository(Template::class)->findAll();

        return $this->render('therapy/scheme/templates-list.html.twig', [
            'templates' => $templates,
        ]);
    }
}
