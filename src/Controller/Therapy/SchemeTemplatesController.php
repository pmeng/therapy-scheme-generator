<?php

namespace App\Controller\Therapy;

use App\Entity\Therapy\Scheme;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SchemeTemplatesController extends AbstractController
{
    const PAGINATION_PAGE = 5;

    protected EntityManagerInterface $entityManager;
    protected PaginatorInterface $paginator;

    public function __construct(
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ) {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/templates/list', name: 'app_therapy_scheme_templates_list')]
    public function list(Request $request): Response
    {
        $repository = $this->entityManager->getRepository(Scheme::class);

        $query = $repository
            ->createQueryBuilder('scheme')
            ->setFirstResult($request->query->getInt('page', 0))
            ->setMaxResults(self::PAGINATION_PAGE)
            ->orderBy('scheme.updatedAt', 'DESC')
            ->getQuery()
        ;
        $templates = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::PAGINATION_PAGE
        );

        return $this->render('therapy/scheme-templates/list.html.twig', [
            'templates' => $templates
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/templates/delete/{id}', name: 'app_therapy_scheme_template_delete')]
    public function delete(Request $request, int $id = null): Response
    {
        $template = $this->entityManager->getRepository(Scheme::class)->find($id);

        if ($template) {
            $this->entityManager->remove($template);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_therapy_scheme_templates_list');
    }
}
