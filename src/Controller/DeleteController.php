<?php

namespace App\Controller;

use App\Entity\Masks;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeleteController extends AbstractController
{
    #[Route('/delete/{id}', name: 'app_delete', methods: ['POST'])]
    public function delete(Masks $masks, Request $request, EntityManagerInterface $entityManager): Response
    {
        // On verifie si le token csrf provient bien du formulaire de suppression correspondant a l'ID
        if ($this->isCsrfTokenValid('SUP'. $masks->getId(), $request->get('_token'))) {
            $entityManager->remove($masks); // On marque l'article pour la suppression
            $entityManager->flush(); // On effectue la requête en database
            $this->addFlash('succes', 'Le masque est bien supprimée');
            return $this->redirectToRoute('app_main');
        }
        $this->addFlash('error', 'Token invalide');
        return $this->redirectToRoute('app_main');
    }
}
