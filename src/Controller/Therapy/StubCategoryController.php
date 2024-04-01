<?php

namespace App\Controller\Therapy;

use App\Entity\Therapy\Stub;
use App\Entity\Therapy\StubCategory;
use App\Form\Therapy\StubCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class StubCategoryController extends AbstractController
{
    const PAGINATION_PAGE = 100;

    protected EntityManagerInterface $entityManager;
    protected PaginatorInterface $paginator;

    public function __construct(
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ) {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/category', name: 'app_therapy_stub_categories_list')]
    public function index(Request $request): Response
    {
        $repository = $this->entityManager->getRepository(StubCategory::class);

        $query = $repository
            ->createQueryBuilder('stub_category')
            ->setFirstResult($request->query->getInt('page', 0))
            ->setMaxResults(self::PAGINATION_PAGE)
            ->orderBy('stub_category.categoryOrder', 'ASC')
            ->getQuery()
        ;
        $pagination = $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::PAGINATION_PAGE
        );

        $categories = $repository
            ->createQueryBuilder('stub_category')
            ->select('stub_category.id', 'stub_category.shortName')
            ->orderBy('stub_category.categoryOrder', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    
        $stubCategoryForm = $this->createForm(StubCategoryType::class);
        $stubCategoryForm->handleRequest($request);

        if ($stubCategoryForm->isSubmitted() && $stubCategoryForm->isValid()) {
            $data = $stubCategoryForm->getData();

            return $this->redirectToRoute('app_new_stub_category', [
                'shortName' => $data['shortName'],
                'reportName' => $data['reportName'],
                'categoryOrder' => $data['categoryOrder']
            ]);

        }
        return $this->render('therapy/stub_category/index.html.twig', [
            'controller_name' => 'StubCategoryController',
            'pagination' => $pagination,
            'stubCategoryForm' => $stubCategoryForm->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/category/new', name: 'app_new_stub_category')]
    public function new(Request $request): Response
    {
        $shortName = $request->query->get('shortName');
        $reportName = $request->query->get('reportName');
        $order = $request->query->get('categoryOrder');
        $order = (int)$order;
    
        // Create a new StubCategory entity and set its properties
        $category = new StubCategory();

        $category->setshortName($shortName);
        $category->setreportName($reportName);
        $category->setCategoryOrder($order);
    
        // Persist the entity to the database
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    
        // Redirect to the index page or any other page as needed
        return $this->redirectToRoute('app_therapy_stub_categories_list');
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/category/edit/{id<\d+>}', name: 'app_therapy_stub_category_edit', methods: ['POST'])]
    public function edit(Request $request, int $id): Response
    {
        // Retrieve the name and order parameters
        $shortName = $request->request->get('shortName');
        $reportName = $request->request->get('reportName');
        $order = $request->request->get('categoryOrder');
        $order = (int)$order;
    
        // Retrieve the StubCategory entity from the database
        $stubCategory = $this->entityManager->getRepository(StubCategory::class)->find($id);
    
        // Update the entity with the new data
        $stubCategory->setshortName($shortName);
        $stubCategory->setreportName($reportName);
        $stubCategory->setCategoryOrder($order);
    
        // Persist the changes to the database
        $this->entityManager->flush();
    
        return $this->redirectToRoute('app_therapy_stub_categories_list');

    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/category/delete/{id<\d+>}', name: 'app_therapy_stub_category_delete')]
    public function delete(Request $request, int $id): Response
    {
        $stubCategory = $this->entityManager->getRepository(StubCategory::class)->find($id);

        if ($stubCategory) {
            $this->entityManager->remove($stubCategory);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_therapy_stub_categories_list');
        }

        return new JsonResponse([
            'success' => 0,
            'message' => 'Stub Category not exists'
        ]);
    }
    
    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/category/check-usage/{id}', name: 'app_check_category_usage')]
    public function checkStubUsage(int $id): Response
    {
        $repository = $this->entityManager->getRepository(Stub::class);

        // Check if there are any stubs that use the given category ID
        $stubs = $repository->createQueryBuilder('s')
            ->select('s.id, s.name')
            ->where('s.category = :categoryId')
            ->setParameter('categoryId', $id)
            ->getQuery()
            ->getResult();
    
        $usedStubs = [];
        foreach ($stubs as $stub) {
            $usedStubs[] = [
                'id' => $stub['id'],
                'name' => $stub['name']
            ];
        }
    
        $isUsed = count($usedStubs) > 0;
    
        return new JsonResponse([
            'isUsed' => $isUsed,
            'usedStubs' => $usedStubs
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/stub/category/replace/{id}', name: 'app_therapy_stub_category_replace')]
    public function reassignStubs(Request $request, int $id): Response
    {
        $newCategoryId = $request->request->get('newCategoryId');
        $newStubCategory = $this->entityManager->getRepository(StubCategory::class)->find($newCategoryId);

        if (!$newCategoryId) {
            return new JsonResponse([
                'success' => 0,
                'message' => 'New category ID is required'
            ]);
        }
    
        $repository = $this->entityManager->getRepository(Stub::class);
        $stubs = $repository->createQueryBuilder('s')
            ->where('s.category = :categoryId')
            ->setParameter('categoryId', $id)
            ->getQuery()
            ->getResult();
        
        if (empty($stubs)) {
            return new JsonResponse([
                'success' => 0,
                'message' => 'No stubs found for the given category ID'
            ]);
        }
    
        foreach ($stubs as $stub) {
            $stub->setCategory( $newStubCategory );
            $this->entityManager->persist($stub);
        }
        // Remove the stub category after reassignment
        $stubCategory = $this->entityManager->getRepository(StubCategory::class)->find($id);
    
        $this->entityManager->flush();
    
    
        if ($stubCategory) {
            $this->entityManager->remove($stubCategory);
            $this->entityManager->flush();
        }
    
        return new JsonResponse([
            'success' => 1
        ]);
    }
    
}
