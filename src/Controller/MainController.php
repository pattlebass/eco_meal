<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(PackageRepository $packageRepository) : Response
    {
        $packages = $packageRepository->findAvailable();
        return $this->render('home/index.html.twig', [
            'packages' => $packages,
        ]);
    }
}
