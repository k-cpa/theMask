<?php

namespace App\Controller;

use App\Repository\MasksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GalleryController extends AbstractController
{
    #[Route('/gallery', name: 'app_gallery')]
    public function index(MasksRepository $maskRepository): Response
    {
        $masks = $maskRepository->findAll();
        return $this->render('pages/gallery.html.twig', [
            'masks' => $masks,
        ]);
    }
}
