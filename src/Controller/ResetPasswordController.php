<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

final class ResetPasswordController extends AbstractController
{
    #[Route('/reset/password', name: 'app_forgot_password')]
    public function request(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $resetRequestForm = $this->createForm(ResetPasswordRequestType::class); 
        $resetRequestForm->handleRequest($request);

        if ($resetRequestForm->isSubmitted() && $resetRequestForm->isValid()) {
            $email = $resetRequestForm->get('email')->getData(); // Récupération du champ email dans une variable
            $user = $userRepository->findOneBy(['email' => $email]);

            if($user) {
                $token = Uuid::v4()->toRfc4122(); // Universally Unique Identifier permet de créer un Token unique
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt((new \DateTime())->modify('+1 hour'));
                // On stocke la date d'expiration du token dans l'objet user
                $entityManager->flush();

                $resetLink = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                // Création du lien intégrant le token
                $email = (new Email()) // création du mail
                    ->from('07148281344577@mailtrap.io')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->text("Voici votre lien de réinitialisation : $resetLink");
                    // Intégration du lien dans le mail 
                    $mailer->send($email);
                    // Envoi du mail
                    $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');

                    return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Aucun utilisateur trouvé pour cet email');
        }

        return $this->render('reset_password/password_request.html.twig', [
            'resetRequestForm' => $resetRequestForm->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHash): Response
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]); // Requete DQL pour récupérer l'objet User associé au token
        if (!$user || !$user->isResetTokenValid()) {
            // On vérifie si l'objet User n'a pas été trouvé ou si le token est invalide (via méthode créé dans le User)
            $this->addFlash('danger', 'Ce lien de réinitialisation est invalide ou expiré');
            return $this->redirectToRoute('app_forgot_password');
        }

        $resetForm = $this->createForm(ResetPasswordType::class);
        $resetForm->handleRequest($request);

        if ($resetForm->isSubmitted() && $resetForm->isValid()) {
            $password = $resetForm->get('password')->getData();
            $hashedPassword = $passwordHash->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);

            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('reset_password/reset_password.html.twig', [
            'resetForm' => $resetForm->createView(),
        ]);
    }
}
