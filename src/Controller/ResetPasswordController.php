<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset-password', name: 'app_forgot_password_request')]
    public function request(
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetPasswordToken($token);
                $user->setResetPasswordTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
                $entityManager->persist($user);
                $entityManager->flush();

                $resetUrl = $urlGenerator->generate('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $emailMessage = (new TemplatedEmail())
                    ->from(new Address('no-reply@julienautomobiles.fr', 'Julien Automobiles'))
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->htmlTemplate('emails/reset_password.html.twig')
                    ->context([
                        'resetPasswordUrl' => $resetUrl,
                        'expiresAt' => $user->getResetPasswordTokenExpiresAt(),
                        'user' => $user,
                    ]);

                $mailer->send($emailMessage);
            }

            return $this->redirectToRoute('app_check_email');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('security/check_email.html.twig');
    }

    #[Route('/reset-password/reset/{token}', name: 'app_reset_password')]
    public function reset(
        string $token,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->findOneBy(['resetPasswordToken' => $token]);

        if (!$user || !$user->getResetPasswordTokenExpiresAt() || $user->getResetPasswordTokenExpiresAt() < new \DateTimeImmutable()) {
            $this->addFlash('danger', 'Ce lien de réinitialisation est invalide ou expiré.');

            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $user->clearResetPasswordToken();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a bien été réinitialisé. Vous pouvez maintenant vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
