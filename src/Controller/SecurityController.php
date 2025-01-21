<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{


    // SIGN IN USERS
    #[Route('/signin', name: 'app_signin')]
    public function signIn(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHash): Response
    {
        $user = new User();

        $formSignIn = $this->createForm(LoginType::class, $user);
        $formSignIn->handleRequest($request);

        if ($formSignIn->isSubmitted() && $formSignIn->isValid()) {

            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $passwordHash->hashPassword($user, $formSignIn->get('password')->getData())
            );

            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', 'Votre profil est enregistré sur le site');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/signin.html.twig', [
            'formSignIn' => $formSignIn->createView(),
        ]);
    }

    // LOGIN USERS
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // LOGOUT USERS
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
