<?php
namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/order')]
class OrderController extends AbstractController
{

    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository): JsonResponse
    {
        $orders = $orderRepository->findAll();

        if (count($orders) > 0) 
        {
            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No orders found.'], Response::HTTP_NOT_FOUND);
    }

    private function orderModel(Order $order): array
    {
        return 
        [
            'id'  => $order->getId(),
            'status'  => $order->getStatus(),
            'address'  => $order->getAddress(),
            'price'  => $order->getPrice(),
            'client'  => $order->getClient(),
            'products'  => $order->getProducts(),
            'date'  => $order->getDate(),
        ];
    }

    #[Route('/new', name: 'app_order_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $status = $data["status"];
        $address = $data["address"];
        $price = $data["price"];
        $client = $data["client"];
        $products = explode(",", $data["products"]);
        $date = $data["date"];

        $order = new Order();
        $order->setStatus($status);
        $order->setAddress($address);
        $order->setPrice($price);
        $order->setClient($client);
        $order->setDate($date);

        foreach ($products as $stringProduct) {
            $product = $entityManager->getRepository(Product::class)->findOneBy(['name' => $stringProduct]);
            $order->addProduct($product);
        }

        $errors = $validator->validate($order);
        if (count($errors)  > 0) 
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        
        $entityManager->persist($order);
        $entityManager->flush();
        return new JsonResponse (['message' => 'Order created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, string $id): Response
    {
        $order = $entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$order)
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        
        $order = $this->orderModel($order);
        return new JsonResponse($order, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $status = $data["status"];
        $address = $data["address"];
        $price = $data["price"];
        $client = $data["client"];
        $products = $data["products"];
        $date = $data["date"];

        $order = $entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$order)
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        
        $order->setStatus($status);
        $order->setAddress($address);
        $order->setPrice($price);
        $order->setClient($client);
        $order->setDate($date);

        foreach ($products as $stringProduct) {
            $product = $entityManager->getRepository(Product::class)->findOneBy(['name' => $stringProduct]);
            $order->addProduct($product);
        }

        $errors = $validator->validate($order);
        if (count($errors)  > 0)
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        
        $entityManager->flush();
        return new JsonResponse (['message' => 'Order updated successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $order = $entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$order)
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        
        $entityManager->remove($order);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Order deleted successfully'], Response::HTTP_OK);
    }

    #[Route('/pending', name: 'app_order_pending', methods: ['GET'])]
    public function getPendingOrders(OrderRepository $orderRepository): JsonResponse
    {
        $orders = $orderRepository->findBy(['status' => 'pending']);

        if (count($orders) > 0) 
        {
            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No pending orders found.'], Response::HTTP_NOT_FOUND);
    }

}