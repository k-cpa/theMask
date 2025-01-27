<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if($contactForm ->isSubmitted() && $contactForm->isValid()) {
            $data = $contactForm->getData(); // Récupération des champs du formulaire

            $email = (new Email())
                ->from('07148281344577@mailtrap.io')
                ->to('kevcampana@gmail.com')
                ->subject('Nouveau message de contact')
                ->text(
                    "email: {$data['email']}\n".
                    "Message: {$data['message']}"
                );
                $mailer->send($email);
        }

        return $this->render('pages/contact.html.twig', [
            'contactForm' => $contactForm->createView(),
        ]);
    }
}
