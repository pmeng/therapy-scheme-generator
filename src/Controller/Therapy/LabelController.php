<?php

namespace App\Controller\Therapy;

use App\Entity\Therapy\Label;
use App\Form\Therapy\LabelType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LabelController extends AbstractController
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

    #[Route('/{_locale<%app.supported_locales%>}/therapy/labels', name: 'app_therapy_labels_list')]
    public function listLabels(Request $request): Response
    {
        $repository = $this->entityManager->getRepository(Label::class);
        $search = $request->get('label_name');

        $query = $repository
            ->createQueryBuilder('label')
            ->setFirstResult($request->query->getInt('page', 0))
            ->setMaxResults(self::PAGINATION_PAGE)
        ;

        if ($search !== null) {
            $query->where(
                $query->expr()->like(
                    'label.shortName',
                    ':search'
                )
            )->setParameter('search', '%' . $search . '%');
        }

        $pagination = $this->paginator->paginate(
            $query->getQuery(),
            $request->query->getInt('page', 1),
            self::PAGINATION_PAGE
        );

        return $this->render('therapy/label/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    
    #[Route('/{_locale<%app.supported_locales%>}/therapy/label/{id<\d+>}', name: 'app_therapy_label_edit')]
    public function editLabel(Request $request, int $id): Response
    {
        $label = $this->entityManager->getRepository(Label::class)->find($id);
        
        if (!$label) {
            // TODO throw exception
        }
        
        $labelForm = $this->createForm(LabelType::class, $label);
        $labelForm->handleRequest($request);
        
        if ($labelForm->isSubmitted() && $labelForm->isValid()) {
            $data = $labelForm->getData();
            
            $label->setShortName($data->getShortName());
            $label->setReportName($data->getReportName());
            
            $this->entityManager->persist($label);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('app_therapy_labels_list');
        }
        
        return $this->render('therapy/label/edit.html.twig', [
            'labelForm' => $labelForm->createView(),
        ]);
    }
}
