<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Consumer;
use App\Entity\Package;
use App\Form\BusinessFormType;
use App\Form\PackageFormType;
use App\Repository\BusinessRepository;
use App\Repository\ConsumerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConsumerController extends AbstractController
{
    #[Route('/consumer', name: 'app_consumer')]
    public function index(ConsumerRepository $consumerRepository): Response
    {
        $consumers = $consumerRepository->findAll();
        return $this->render('consumer/index.html.twig', [
            'consumers' => $consumers,
        ]);
    }

    #[Route('/consumer/{id}', name: 'app_consumer_view', methods: ['GET'])]
    public function view(Consumer $consumer): Response
    {
        $user = $this->getUser();
        if (!($consumer->getUser()->getId() == $user->getId() || $this->isGranted("ROLE_ADMIN")))
        {
            return $this->redirectToRoute('app_index');
        }
        return $this->render('consumer/view.html.twig', [
            'consumer' => $consumer,
        ]);
    }

    #[Route('/consumer/{id}', name: 'app_consumer_delete', methods: ['POST'])]
    public function delete(Consumer $consumer, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted("ROLE_CONSUMER");
        $user = $this->getUser();
        if (!($consumer->getUser()->getId() == $user->getId() || $this->isGranted("ROLE_ADMIN")))
        {
            return $this->redirectToRoute('app_index');
        }
        $entityManager->remove($consumer);
        $entityManager->flush();
        return $this->redirectToRoute('app_index');
    }
}
