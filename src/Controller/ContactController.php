<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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
    public function createContact(
        ContactFormType $contactType, Request $request, EntityManagerInterface $emanager,
        MailerInterface $mailer
    ) {
        /** récupérer les données de l'user s'il est connecté pour pré-remplir le formulaire */
        $contact = new Contact();
        if($this->getUser()) {
            $contact->setFullName($this->getUser()->getFullName())->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm($contactType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
            //dd($form->getData()->getFullName());
            //dd($contact->getEmail());

            /** sending mail */
            $email = (new TemplatedEmail())
                        ->from($contact->getEmail())->to('admin@gesrecipe.com')
                        ->subject($contact->getSubject())
                        ->htmlTemplate('emails/contact.html.twig')
                        ->context(['contact' => $contact]);
            $mailer->send($email);
            /** end sending mail */

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
