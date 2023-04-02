<?php

namespace App\Controller\Therapy;

use App\Entity\Template;
use App\Entity\Therapy\Label;
use App\Entity\Therapy\Scheme;
use App\Form\TherapySchemeType;
use App\Form\SchemeTemplateType;
use App\Repository\Therapy\SchemeRepository;
use App\Service\SchemeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/create', name: 'app_therapy_scheme_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(TherapySchemeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $labels = $form->get('labels')->getData(); // * not mapped

            $formData = $form->getData();

            $scheme = new Scheme();
            $targets = json_decode($formData['targets'], true);
            $scheme->setTargets($targets);

            $comments = json_decode($formData['comments'], true);
            if ($comments === null) {
                $comments = [];
            }
            $scheme->setComments($comments);

            $scheme->setSuppress($formData['suppress']);
            $scheme->setExcerpt($formData['excerpt']);

            $scheme->setCreatedAt(new \DateTimeImmutable());
            $scheme->setUpdatedAt(new \DateTimeImmutable());
            $labelIDs = $labels->map(fn (Label $label) => $label->getId())->toArray();
            $scheme->setSelectedLabels($labelIDs);

            // * Store the scheme in the session
            $session = $request->getSession();
            $session->set('scheme', $scheme);
            return $this->redirectToRoute('app_therapy_scheme_saveAsTemplate');
        }

        return $this->render('therapy/scheme/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function validateRequestData(array $requestData): ?string
    {
        if (!isset($requestData['selectedLabels'])) {
            return 'Missing selectedLabels';
        }

        if (!isset($requestData['currentComments'])) {
            return 'Missing currentComments';
        }

        if (!isset($requestData['notCheckedCheckboxes'])) {
            return 'Missing notCheckedCheckboxes';
        }

        if (!isset($requestData['currentLanguage'])) {
            return 'Missing currentLanguage';
        }
        if (!in_array($requestData['currentLanguage'], ['en', 'de'])) {
            return 'Invalid currentLanguage';
        }

        if (!isset($requestData['suppress'])) {
            return 'Missing suppress';
        }
        if (!is_bool($requestData['suppress'])) {
            return 'Invalid suppress';
        }

        if (!isset($requestData['excerpt'])) {
            return 'Missing excerpt';
        }
        if (!is_bool($requestData['excerpt'])) {
            return 'Invalid excerpt';
        }

        return "";
    }


    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/generateForm', name: 'app_therapy_scheme_generateForm', methods: ['POST'])]
    public function generateForm(Request $request, SchemeService $schemeService): Response
    {
        // * Validation
        $requestData = json_decode($request->getContent(), true);
        $validationError = $this->validateRequestData($requestData);
        if (strlen($validationError) > 0) {
            return new JsonResponse(['error' => $validationError], 400);
        }

        $selectedLabels = $requestData['selectedLabels'];
        $currentLanguage = $requestData['currentLanguage'];
        $currentComments = $requestData['currentComments'];
        $notCheckedCheckboxes = $requestData['notCheckedCheckboxes'];
        $suppress = $requestData['suppress'];
        $excerpt = $requestData['excerpt'];

        $newTbody = $schemeService->generateTbody(
            $selectedLabels,
            $suppress,
            $currentComments,
            $notCheckedCheckboxes,
            $excerpt,
            $currentLanguage
        );

        return new JsonResponse($newTbody);
    }


    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/generateReport', name: 'app_therapy_scheme_generateReport', methods: ['POST'])]
    public function generateReport(Request $request, SchemeService $schemeService): Response
    {
        // * Validation
        $requestData = json_decode($request->getContent(), true);
        $validationError = $this->validateRequestData($requestData);
        if (strlen($validationError) > 0) {
            return new JsonResponse(['error' => $validationError], 400);
        }

        $selectedLabels = $requestData['selectedLabels'];
        $currentLanguage = $requestData['currentLanguage'];
        $currentComments = $requestData['currentComments'];
        $notCheckedCheckboxes = $requestData['notCheckedCheckboxes'];
        $suppress = $requestData['suppress'];
        $excerpt = $requestData['excerpt'];

        $reportContent = $schemeService->generateReport(
            $selectedLabels,
            $suppress,
            $currentComments,
            $notCheckedCheckboxes,
            $excerpt,
            $currentLanguage
        );

        $session = $request->getSession();
        $session->set('reportContent', $reportContent);
        $session->set('reportExcerpt', $excerpt);

        return new JsonResponse(['success' => true], 200);
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

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/generate/html', name: 'app_therapy_scheme_generate_html', methods: ['GET'])]
    public function generateHtml(Request $request): Response
    {
        $session = $request->getSession();
        $reportContent = $session->get('reportContent');
        $reportExcerpt = $session->get('reportExcerpt');
        if (!$reportContent) {
            return $this->redirectToRoute('app_therapy_scheme_create'); // todo changeName
        }
        $reportContent = "<tbody>$reportContent</tbody>";
        return $this->render('therapy/scheme/html-template.html.twig', [
            'reportContent' => $reportContent,
            'reportExcerpt' => $reportExcerpt,
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


    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/searchRedirector', name: 'app_therapy_scheme_search_redirector')]
    public function searchRedirector(Request $request): Response
    {
        $searchValue = $request->get('searchName_scheme');
        if ($searchValue !== null && strlen($searchValue) > 0) {
            return $this->redirectToRoute('app_therapy_scheme_search', ['searchValue' => $searchValue]);
        } else {
            return $this->redirect($request->headers->get('referer'));
        }
    }


    const NB_PER_PAGE = 5;

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/search?searchValue={searchValue}', name: 'app_therapy_scheme_search')]
    public function searchLabels(Request $request, SchemeRepository $schemeRepository, string $searchValue): Response
    {
        if ($searchValue !== null) {
            $templates = $schemeRepository->createQueryBuilder('t')
                ->where('t.name LIKE :searchValue')
                ->setParameter('searchValue', '%' . $searchValue . '%')
                ->getQuery()
                ->getResult();
        } else {
            $templates = $schemeRepository->findAll();
        }

        $pagination = $this->paginator->paginate(
            $templates,
            $request->query->getInt('page', 1),
            self::NB_PER_PAGE
        );

        return $this->render('therapy/scheme/searchResult.html.twig', [
            'templates' => $pagination,
            'searchValue' => $searchValue,
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/templates/list', name: 'app_therapy_saved_templates', methods: ['GET', 'POST'])]
    public function loadTemplates(Request $request, SchemeRepository $schemeRepository): Response
    {
        $templates = $schemeRepository->findAll();

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
