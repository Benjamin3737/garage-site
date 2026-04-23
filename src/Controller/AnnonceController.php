<?php
// src/Controller/AnnonceController.php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AnnonceController extends AbstractController
{
    #[Route('/annonces', name: 'app_annonces')]
    public function index(AnnonceRepository $repo): Response
    {
        $annonces = $repo->findAll();
        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    #[Route('/annonce/new', name: 'app_annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $annonce = new Annonce();
            $annonce->setTitre($request->request->get('titre'));
            $annonce->setPrix($request->request->get('prix'));
            $annonce->setKilometrage($request->request->get('kilometrage'));
            $annonce->setAnnee($request->request->get('annee'));
            $annonce->setCarburant($request->request->get('carburant'));
            $annonce->setDescription($request->request->get('description'));
            $annonce->setCreatedAt(new \DateTime());

            $file = $request->files->get('image');
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('images_directory'), $filename);
                $annonce->setImage($filename);
            }

            $em->persist($annonce);
            $em->flush();

            return $this->redirectToRoute('app_annonces');
        }

        return $this->render('annonce/new.html.twig');
    }

    #[Route('/annonce/{id}/delete', name: 'app_annonce_delete', methods: ['POST'])]
    public function delete(Annonce $annonce, EntityManagerInterface $em): Response
    {
        if ($annonce->getImage()) {
            $imagePath = $this->getParameter('images_directory') . '/' . $annonce->getImage();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $em->remove($annonce);
        $em->flush();

        return $this->redirectToRoute('app_annonces');
    }
}
