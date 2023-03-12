<?php

namespace App\Controller\Therapy;

use App\Entity\Template;
use App\Entity\Therapy\Label;
use App\Entity\Therapy\Scheme;
use App\Form\SchemeTemplateType;
use App\Repository\Therapy\SchemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;

class SchemeController extends AbstractController
{
    const ITEMS_PER_PAGE = 5;

    protected EntityManagerInterface $entityManager;
    protected PaginatorInterface $paginator;

    public function __construct(
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ) {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
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

    /*#[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/save/template/{templateId}', name: 'app_therapy_scheme_save_template', methods: ['POST'])]
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
    }*/

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/templates/list', name: 'app_therapy_saved_templates', methods: ['GET', 'POST'])]
    public function loadTemplates(Request $request, SchemeRepository $schemeRepository): Response
    {
        $searchValue = $request->get('searchName_scheme');

        if ($searchValue) {
            $templates = $schemeRepository->createQueryBuilder('t')
                ->where('t.name LIKE :searchValue')
                ->setParameter('searchValue', '%' . $searchValue . '%')
                ->getQuery()
                ->getResult();
        } else {
            $templates = $schemeRepository->findAll();
        }

        $templates = $this->paginator->paginate(
            $templates,
            $request->query->getInt('page', 1),
            self::ITEMS_PER_PAGE
        );

        return $this->render('therapy/scheme/templates-list.html.twig', [
            'templates' => $templates,
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/delete/template/{templateId}', name: 'app_therapy_scheme_delete')]
    public function deleteTemplate(SchemeRepository $schemeRepository, $templateId): Response
    {
        $template = $schemeRepository->find($templateId);
        if ($template) {
            $this->entityManager->remove($template);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_therapy_saved_templates');
    }
}
