<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (is_string($plainPassword) && strlen($plainPassword) > 0 && strlen($plainPassword) < 6) {
                $form->get('plainPassword')->addError(new FormError('Votre mot de passe doit contenir au moins 6 caractères.'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $form->addError(new FormError('Cet email est déjà utilisé.'));
            } else {
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                $user->setRoles(['ROLE_USER']);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre compte a bien été créé. Vous pouvez maintenant vous connecter.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
