<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($request->isMethod('POST')) {
            $firstName = trim((string) $request->request->get('first_name', ''));
            $lastName = trim((string) $request->request->get('last_name', ''));
            $email = trim((string) $request->request->get('email', ''));
            $phone = trim((string) $request->request->get('phone', ''));
            $subject = trim((string) $request->request->get('subject', ''));
            $content = trim((string) $request->request->get('content', ''));

            if (!$firstName || !$lastName || !$email || !$content) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires du formulaire de contact.');

                return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
            }

            $message = new ContactMessage();
            $message
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setPhone($phone ?: null)
                ->setSubject($subject ?: null)
                ->setContent($content)
                ->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.');
        }

        return $this->redirectToRoute('app_home', ['_fragment' => 'contact']);
    }
}
