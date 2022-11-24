<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/fix', name: 'fix')]
    public function index(): Response
    {
        return $this->render('pages/contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }

    #[Route('/contact', name: 'recipe_contact')]
    public function createContact(ContactFormType $contactType, Request $request, EntityManagerInterface $emanager) {
        /** récupérer les données de l'user s'il est connecté */
        $contact = new Contact();
        if($this->getUser()) {
            $contact->setFullName($this->getUser()->getFullName())->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm($contactType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            $emanager->persist($form->getData());
            $emanager->flush();
            $this->addFlash('success', 'Votre message a bien été envoyé !');
            return $this->redirectToRoute('recipe_contact');
        }

        return $this->render('pages/contact/index.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }
}
