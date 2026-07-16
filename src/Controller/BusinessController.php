<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\FavoriteBusiness;
use App\Entity\Package;
use App\Form\BusinessFormType;
use App\Form\BusinessRegistrationFormType;
use App\Form\UserFormType;
use App\Form\PackageFormType;
use App\Repository\BusinessRepository;
use App\Repository\FavoriteBusinessRepository;
use App\Repository\OrderRepository;
use App\Repository\PackageRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BusinessController extends AbstractController
{
    #[Route('/business', name: 'app_business')]
    public function index(BusinessRepository $businessRepository): Response
    {
        $businesses = $businessRepository->findAll();
        return $this->render('business/index.html.twig', [
            'businesses' => $businesses,
        ]);
    }

    #[Route('/business/{id}', name: 'app_business_view', methods: ['GET'])]
    public function view(Business $business, PackageRepository $packageRepository, FavoriteBusinessRepository $favoriteBusinessRepository): Response
    {
        $isFavorite = false;
        if ($this->isGranted('ROLE_USER') and !$this->isGranted('ROLE_ADMIN')) {
            $isFavorite = $favoriteBusinessRepository->findOneBy([
                'business' => $business,
                'consumer' => $this->getUser()->getConsumer()
            ]) != null;
        }

        $availablePackages = $packageRepository->findAvailableByBusiness($business);

        return $this->render('business/view.html.twig', [
            'business' => $business,
            'packages' => $availablePackages,
            'isFavorite' => $isFavorite
        ]);
    }

    #[Route('/business/{id}', name: 'app_business_delete', methods: ['POST'])]
    public function delete(Business $business, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $user = $this->getUser();
        if (!($business->getUser()->getId() == $user->getId() || $this->isGranted("ROLE_ADMIN")))
        {
            return $this->redirectToRoute('app_index');
        }
        $entityManager->remove($business);
        $entityManager->flush();
        return $this->redirectToRoute('app_business');
    }

    #[Route('/new/business', name: 'app_business_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $business = new Business();
        $form = $this->createForm(BusinessFormType::class, $business);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($business);
            $entityManager->flush();
            return $this->redirectToRoute('app_business');
        }

        return $this->render('business/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/business/{id}/update', name: 'app_business_update', methods: ['GET', 'POST'])]
    public function update(Business $business, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BusinessFormType::class, $business);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($business);
            $entityManager->flush();
            return $this->redirectToRoute('app_business');
        }

        return $this->render('business/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/business/{id}/update-user', name: 'app_business_update_user', methods: ['GET', 'POST'])]
    public function updateUser(Business $business, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!($business->getUser()->getId() == $user->getId()))
        {
            return $this->redirectToRoute('app_index');
        }

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_business');
        }

        return $this->render('business/update-user.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/business/{id}/new-package', name: 'app_business_new_package', methods: ['GET', 'POST'])]
    public function newPackage(Business $business, Request $request, ImageUploader $imageUploader, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!($business->getUser()->getId() == $user->getId()))
        {
            return $this->redirectToRoute('app_index');
        }

        $package = new Package();
        $package->setBusiness($business);
        $package->setCreatedAt(new \DateTimeImmutable());
        $form = $this->createForm(PackageFormType::class, $package);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedImage = $form['uploadedImage']->getData();
            $existingImage = $form['image']->getData();
            if ($uploadedImage) {
                $packageImage = $imageUploader->uploadPackageImage($uploadedImage, $business, $entityManager);
                $package->setImage($packageImage);
            } elseif ($existingImage) {
                $package->setImage($existingImage);
            }

            $entityManager->persist($package);
            $entityManager->flush();

            return $this->redirectToRoute('app_package');
        }

        return $this->render('package/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/business/{id}/update-package/{packageId}', name: 'app_business_update_package', methods: ['GET', 'POST'])]
    public function updatePackage(Business $business, int $packageId, ImageUploader $imageUploader, Request $request, EntityManagerInterface $entityManager): Response
    {
        $package = $entityManager->getRepository(Package::class)->find($packageId);
        $package->setCreatedAt(new \DateTimeImmutable());
        $form = $this->createForm(PackageFormType::class, $package);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedImage = $form['uploadedImage']->getData();
            $existingImage = $form['image']->getData();
            if ($uploadedImage) {
                $packageImage = $imageUploader->uploadPackageImage($uploadedImage, $business, $entityManager);
                $package->setImage($packageImage);
            } elseif ($existingImage) {
                $package->setImage($existingImage);
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_business_view', ['id' => $business->getId()]);
        }

        return $this->render('package/update.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/business/{id}/orders', name: 'app_business_orders', methods: ['GET'])]
    public function orders(Business $business, OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        if (!($business->getUser()->getId() == $user->getId() || $this->isGranted("ROLE_ADMIN")))
        {
            return $this->redirectToRoute('app_index');
        }

        $orders = $orderRepository->findByBusiness($business);

        return $this->render('order/business-orders.html.twig', [
            'business' => $business,
            'orders' => $orders,
        ]);
    }

    // probabil ca trebuia sa fac doua rute separate: add_to si remove
    #[Route('/business/{id}/toggle-favorite', name: 'app_business_toggle_favorite', methods: ['POST'])]
    public function favorite(Business $business, EntityManagerInterface $em, FavoriteBusinessRepository $favoriteRepository): Response
    {
        $favorite = $favoriteRepository->findOneBy([
            'business' => $business,
            'consumer' => $this->getUser()->getConsumer()
        ]);

        $consumer = $this->getUser()->getConsumer();

        if ($favorite) {
            $em->remove($favorite);
        } else {
            $favorite = new FavoriteBusiness();
            $favorite->setConsumer($consumer);
            $favorite->setBusiness($business);

            $em->persist($favorite);
        }

        $em->flush();

        return $this->redirectToRoute('app_business_view', ['id' => $business->getId()]);
    }
}
