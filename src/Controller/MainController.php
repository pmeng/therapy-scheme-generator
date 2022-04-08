<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/{_locale<%app.supported_locales%>?}', name: 'app_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_main');
    }

    #[Route('/{_locale<%app.supported_locales%>?}/main', name: 'app_main')]
    public function main(Request $request): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
