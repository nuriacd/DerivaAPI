<?php
namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\Restaurant;
use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Repository\RestaurantRepository;
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
    public function index(OrderRepository $orderRepository, OrderProductRepository $orderProductRepository): JsonResponse
    {
        $orders = $orderRepository->findAll();

        if (count($orders) > 0) 
        {
            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order, $orderProductRepository);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No orders found.'], Response::HTTP_NOT_FOUND);
    }

    private function orderModel(Order $order, OrderProductRepository $orderProductRepository): array
    {

        $products = $orderProductRepository->findBy(['order_id' => $order]);

        return 
        [
            'id'  => $order->getId(),
            'status'  => $order->getStatus(),
            'address'  => $order->getAddress(),
            'price'  => $order->getPrice(),
            'client'  => $order->getClient(),
            'products'  => $products,
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
        $products = $data["products"];
        $date = $data["date"];
        $restaurant = $data["restaurant"];

        $restaurant = $entityManager->getRepository(Restaurant::class)->find($restaurant);

        $order = new Order();
        $order->setStatus($status);
        $order->setAddress($address);
        $order->setPrice($price);
        $order->setClient($client);
        $order->setDate($date);
        $order->setRestaurant($restaurant);

        $errors = $validator->validate($order);
        if (count($errors)  > 0) 
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        
        $entityManager->persist($order);
        $entityManager->flush();

        foreach ($products as $stringProduct) {
            $errors = $this->orderProductAdd($entityManager, $validator, $stringProduct, $order);

            if ($errors > 0) 
                return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return new JsonResponse (['message' => 'Order created successfully'], Response::HTTP_CREATED);
    }

    private function orderProductAdd(EntityManagerInterface $entityManager, ValidatorInterface $validator, string $stringProduct, Order $order): int 
    {
        $product = $entityManager->getRepository(Product::class)->find($stringProduct['id']);

        $orderProduct = new OrderProduct();
        $orderProduct->setProductId($product);
        $orderProduct->setOrderId($order);

        $orderProduct->setQuantity($stringProduct["quantity"]);
        $errors = $validator->validate($orderProduct);

        return count($errors);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, string $id, OrderProductRepository $orderProductRepository): Response
    {
        $order = $entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$order)
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        
        $order = $this->orderModel($order, $orderProductRepository);
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

        $order = $entityManager->getRepository(Order::class)->find($id);
        if (!$order)
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        
        $order->setStatus($status);
        $order->setAddress($address);
        $order->setPrice($price);
        $order->setClient($client);
        $order->setDate($date);

        $errors = $validator->validate($order);
        if (count($errors)  > 0) 
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);

        foreach ($order->getOrderProducts() as $product) {
            $order->removeOrderProduct($product);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        foreach ($products as $stringProduct) {
            $errors = $this->orderProductAdd($entityManager, $validator, $stringProduct, $order);

            if ($errors > 0) 
                return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return new JsonResponse (['message' => 'Order updated successfully'], Response::HTTP_OK);
    }
    
    #[Route('/{id}/status', name: 'app_edit_status', methods: ['PUT'])]
    public function editStatus(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $status = $data["status"];

        $order = $entityManager->getRepository(Order::class)->find($id);
        if (!$order)
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        
        $order->setStatus($status);

        $errors = $validator->validate($order);
        if (count($errors)  > 0)
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        
        $entityManager->flush();
        return new JsonResponse (['message' => 'Order status updated successfully'], Response::HTTP_OK);
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
    public function getPendingOrders(OrderRepository $orderRepository, OrderProductRepository $orderProductRepository): JsonResponse
    {
        $orders = $orderRepository->findBy(['status' => 'pending']);

        if (count($orders) > 0) 
        {
            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order, $orderProductRepository);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No pending orders found.'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/restaurant/{id}', name: 'app_order_restaurant', methods: ['GET'])]
    public function getRestaurantOrders(OrderRepository $orderRepository, $id, OrderProductRepository $orderProductRepository): JsonResponse
    {
        $orders = $orderRepository->findBy(['restaurant' => $id]);

        if (count($orders) > 0) 
        {
            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order, $orderProductRepository);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'No orders found for this restaurant.'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/restaurant/{id}/can-deliver', name: 'app_order_can_deliver', methods: ['POST'])]
    public function canDeliver(Request $request, RestaurantRepository $restaurantRepository, $id): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $city = $data["city"];
        
        $restaurant = $restaurantRepository->find($id);

        if ($restaurant && $restaurant->getDeliveryCity() === $city) {
            return new JsonResponse(['message' => 'Delivery is possible in your city.'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Delivery is not possible in your city.'], Response::HTTP_NOT_FOUND);
    }
}