<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Consumer;
use App\Entity\Order;
use App\Entity\Package;
use App\Form\BusinessFormType;
use App\Form\PackageFormType;
use App\Repository\BusinessRepository;
use App\Repository\ConsumerRepository;
use App\Repository\OrderRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Order $order, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted("ROLE_CONSUMER");
        $user = $this->getUser();
        $consumer = $order->getConsumer();
        if (!($consumer->getUser()->getId() == $user->getId() || $this->isGranted("ROLE_ADMIN")))
        {
            return $this->redirectToRoute('app_index');
        }
        $entityManager->remove($order);
        $entityManager->flush();
        return $this->redirectToRoute('app_consumer_view', ['id' => $consumer->getId()]);
    }

    #[Route('/package/{id}/new-order', name: 'app_order_new', methods: ['POST'])]
    public function new(Package $package, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $order->setPackage($package);
        $order->setConsumer($this->getUser()->getConsumer());
        $order->setCreatedAt(new DateTimeImmutable());

        $entityManager->persist($order);
        $entityManager->flush();
        return $this->redirectToRoute('app_package');
    }
}
