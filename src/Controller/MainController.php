<?php

namespace App\Controller;

use App\Repository\MasksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(MasksRepository $maskRepository): Response
    {

        // Afficher uniquement les 4 derniers ajouts de masques
        $latestMasks = $maskRepository->findBy([], ['createdAt' => 'DESC'], 4);
        return $this->render('pages/index.html.twig', [
            'latestMasks' => $latestMasks,
        ]);
    }
}
