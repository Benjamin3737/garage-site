<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CleaningController extends AbstractController
{
    #[Route('/cleaning', name: 'app_cleaning')]
    public function index(): Response
    {
        return $this->render('cleaning/index.html.twig', [
            'controller_name' => 'CleaningController',
        ]);
    }
}
