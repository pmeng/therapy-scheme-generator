<?php

namespace App\Controller\Therapy;


use App\Entity\Therapy\Stub;
use App\Form\Therapy\StubType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class StubController extends AbstractController
{
    const PAGINATION_PAGE = 5;
    protected EntityManagerInterface $entityManager;
    protected TranslatorInterface $translator;
    protected PaginatorInterface $paginator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        PaginatorInterface $paginator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->paginator = $paginator;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/{_locale<%app.supported_locales%>}/therapy/stubs', name: 'app_therapy_stubs_list')]
    public function index(Request $request): Response
    {
        $repository = $this->entityManager->getRepository(Stub::class);

        $query = $repository
            ->createQueryBuilder('stub')
            ->setFirstResult($request->query->getInt('page', 0))
            ->setMaxResults(self::PAGINATION_PAGE)
            ->getQuery()
        ;
        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::PAGINATION_PAGE
        );

        return $this->render('therapy/stub/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/new', name: 'app_therapy_stub_new')]
    public function newStub(Request $request): Response
    {
        $stubForm = $this->createForm(StubType::class);
        $stubForm->handleRequest($request);

        if ($stubForm->isSubmitted() && $stubForm->isValid()) {
            $data = $stubForm->getData();

            $stub = $this->entityManager
                ->getRepository(Stub::class)
                ->getNewStubObjectFromArray($data);

            return $this->redirectToRoute('app_therapy_stub_edit', ['id' => $stub->getId()]);
        }

        return $this->render('therapy/stub/index.html.twig', [
            'formTitle' => $this->translator->trans('stub_new_form_title'),
            'stubForm' => $stubForm->createView(),
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/edit/{id<\d+>}', name: 'app_therapy_stub_edit')]
    public function editStub(Request $request, int $id): Response
    {
        $repository = $this->entityManager->getRepository(Stub::class);
        $stub = $repository->find($id);

        if (!$stub) {
            // TODO exception about stub not exists
        }

        $stubForm = $this->createForm(StubType::class, $repository->getStubObjectFromEntity($stub));
        $stubForm->handleRequest($request);

        if ($stubForm->isSubmitted() && $stubForm->isValid()) {
            $data = $stubForm->getData();

            $repository->updateEntityFromDto($stub, $data);
        }

        return $this->render('therapy/stub/index.html.twig', [
            'formTitle' => $this->translator->trans('stub_edit_form_title', [
                'stub_name' => $stub->getName()
            ]),
            'stubForm' => $stubForm->createView(),
        ]);
    }
}
