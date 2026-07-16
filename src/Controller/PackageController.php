<?php

namespace App\Controller;

use App\Dto\PackageSearchFilter;
use App\Entity\Business;
use App\Entity\Package;
use App\Form\BusinessFormType;
use App\Form\PackageFiltersType;
use App\Form\PackageFormType;
use App\Repository\BusinessRepository;
use App\Repository\PackageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PackageController extends AbstractController
{
    #[Route('/package', name: 'app_package')]
    public function index(PackageRepository $packageRepository, Request $request): Response
    {
        $filter = new PackageSearchFilter();
        $form = $this->createForm(PackageFiltersType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        $packages = $packageRepository->findAvailableByFilter($filter);

        return $this->render('package/index.html.twig', [
            'packages' => $packages,
            'form' => $form,
        ]);
    }

    #[Route('/package/favorite', name: 'app_package_favorite')]
    public function favorite(PackageRepository $packageRepository, Request $request): Response
    {
        $filter = new PackageSearchFilter();
        $form = $this->createForm(PackageFiltersType::class, $filter, ['method' => 'GET']);
        $form->handleRequest($request);

        $consumer = $this->getUser()->getConsumer();
        $packages = $packageRepository->findAvailableFavoritesByFilter($filter, $consumer);

        return $this->render('package/favorite_index.html.twig', [
            'packages' => $packages,
            'favoriteBusinesses' => $consumer->getFavoriteBusinesses(),
            'form' => $form,
        ]);
    }

    #[Route('/package/{id}', name: 'app_package_view', methods: ['GET'])]
    public function view(Package $package): Response
    {
        return $this->render('package/view.html.twig', [
            'package' => $package,
        ]);
    }

    #[Route('/package/{id}', name: 'app_package_delete', methods: ['POST'])]
    public function delete(Package $package, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($package);
        $entityManager->flush();
        return $this->redirectToRoute('app_package');
    }
}
