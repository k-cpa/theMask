<?php

namespace App\Controller;

use App\Entity\Masks;
use App\Form\AddMaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddMaskController extends AbstractController
{
    #[Route('/add/mask', name: 'app_add_mask')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mask = new Masks();

        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_home');
        }

        $formAddMask = $this->createForm(AddMaskType::class, $mask);

         // Indique a symfony de prendre les données et de les associer au formulaire
         $formAddMask->handleRequest($request);

         // On vérifie si formulaire est soumis et qu'il est valide
         if ($formAddMask->isSubmitted() && $formAddMask->isValid()) {
 
             // Gestion relations Many to Many
             // foreach($pizza->getIngredient() as $Ingredient) {
             //     $Ingredient->addPizza($pizza);
             // }
 
             // On marque les infos de l'objet article prêt a être envoyé en database
             $entityManager->persist($mask);
 
             // On envoie les données
             $entityManager->flush();
 
             // Message get
             $this->addFlash('success', 'Votre masque a été ajouté !');
 
             // Redirection
             return $this->redirectToRoute('app_home');
         }

        return $this->render('pages/addMask.html.twig', [
            'formAddMask' => $formAddMask->createView(),
        ]);
    }
}
