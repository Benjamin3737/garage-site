<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use App\Repository\ContactMessageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(AnnonceRepository $annonceRepository, UserRepository $userRepository, ContactMessageRepository $contactMessageRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }

        return $this->render('admin/dashboard.html.twig', [
            'annoncesCount' => $annonceRepository->count([]),
            'usersCount' => $userRepository->count([]),
            'adminCount' => $userRepository->countAdmins(),
            'messagesCount' => $contactMessageRepository->count([]),
            'recentAnnonces' => $annonceRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'recentMessages' => $contactMessageRepository->findLatest(5),
        ]);
    }
}
