<?php

namespace App\Controller\Therapy;

use App\Entity\Therapy\SchemeSetting;
use App\Form\SchemeSettingType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Therapy\SchemeSettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

class SchemeSettingController extends AbstractController
{

    protected EntityManagerInterface $entityManager;
    protected TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    #[Route('/{_locale<%app.supported_locales%>}/therapy/scheme/settings', name: 'app_therapy_scheme_settings')]
    public function index(Request $request, SchemeSettingRepository $schemeSettingRepository): Response
    {
        $settings = $schemeSettingRepository->findOneBy([]) ?: new SchemeSetting();

        $form = $this->createForm(SchemeSettingType::class, $settings);
        $currentLogo = $settings->getLogo();    
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // Handle file upload
            $uploadedFile = $form['logo']->getData();

            if ($uploadedFile) {
                // Convert the file to binary data
                $logo = file_get_contents($uploadedFile->getPathname());
                $settings->setLogo($logo);
            } else {
                // Keep the current logo if no new file is uploaded
                $settings->setLogo($currentLogo);
            }

            $this->entityManager->persist($settings);
            $this->entityManager->flush();

            $this->addFlash('success', 'Settings saved successfully!');
        }

        return $this->render('therapy/scheme/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}
