<?php
namespace App\Controller;

use App\Entity\Client;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\Restaurant;
use App\Repository\OrderRepository;
use App\Repository\RestaurantRepository;
use DateTime;
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
    public function index(OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $orders = $orderRepository->findAll();

        if (count($orders) > 0) 
        {
            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order,  $entityManager);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No orders found.'], Response::HTTP_NOT_FOUND);
    }

    private function orderModel(Order $order, EntityManagerInterface $entityManager): array
    {

        $products = $entityManager->getRepository(OrderProduct::class)->findBy(['order_id' => $order]);
        
        foreach ($products as $stringProduct) {
            $product = $stringProduct->getProductId();

            $orderProduct = [
                'quantity' => $stringProduct->getQuantity(),
                'product' => [
                    'id'  => $product->getId(),
                    'name'  => $product->getName(),
                    'price'  => $product->getPrice(),
                    'description'  => $product->getDescription(),
                    'image' => $this->generateImageUrl($product->getImage()),
                ]
            ];

            $orderProducts[] = $orderProduct;
        }

        return 
        [
            'id'  => $order->getId(),
            'status'  => $order->getStatus(),
            'address'  => $order->getAddress(),
            'price'  => $order->getPrice(),
            'client'  => $order->getClient()->getEmail(),
            'products'  => $orderProducts,
            'date'  => $order->getDate()->format('d/m/Y'),
            'restaurant'  => $order->getRestaurant()->getId(),
        ];

    }

    #[Route('/new', name: 'app_order_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $status = $data["status"];
        $address = $data["address"];
        $price = $data["price"];
        $client =  $entityManager->getRepository(Client::class)->findOneBy(['email' => $data['client']]);
        $products = $data["products"];
        $date = new DateTime($data["date"]);
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($data['restaurant']);

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
        
        foreach ($products as $stringProduct) {
            $product = $entityManager->getRepository(Product::class)->find($stringProduct['product']['id']);

            $orderProduct = new OrderProduct();
            $orderProduct->setProductId($product);
            $orderProduct->setOrderId($order);

            $orderProduct->setQuantity($stringProduct["quantity"]);
            $errors = $validator->validate($orderProduct);

            if (count($errors) > 0) 
                return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            
            $entityManager->persist($orderProduct);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse (['message' => 'Order created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}/show', name: 'app_order_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, string $id): Response
    {
        $order = $entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);
        if (!$order)
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        
        $order = $this->orderModel($order, $entityManager);
        return new JsonResponse($order, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $status = $data["status"];
        $address = $data["address"];
        $price = $data["price"];
        $client =  $entityManager->getRepository(Client::class)->findOneBy(['email' => $data['client']]);
        $products = $data["products"];
        $date = $data["date"];
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($data['restaurant']);

        $order = $entityManager->getRepository(Order::class)->find($id);
        if (!$order)
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        
        $order->setStatus($status);
        $order->setAddress($address);
        $order->setPrice($price);
        $order->setClient($client);
        $order->setDate($date);
        $order->setRestaurant($restaurant);

        $errors = $validator->validate($order);
        if (count($errors)  > 0) 
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);

        foreach ($order->getOrderProducts() as $product) {
            $order->removeOrderProduct($product);
        }

        foreach ($products as $stringProduct) {
            $product = $entityManager->getRepository(Product::class)->find($stringProduct['product']['id']);

            $orderProduct = new OrderProduct();
            $orderProduct->setProductId($product);
            $orderProduct->setOrderId($order);

            $orderProduct->setQuantity($stringProduct["quantity"]);
            $errors = $validator->validate($orderProduct);

            if (count($errors) > 0) 
                return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            
            $entityManager->persist($orderProduct);
        }

        $entityManager->persist($order);
        $entityManager->flush();

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

    #[Route('/pending/{restaurant}', name: 'app_order_pending', methods: ['GET'])]
    public function getPendingOrders(OrderRepository $orderRepository, EntityManagerInterface $entityManager, string $restaurant): JsonResponse
    {
        $orders = $orderRepository->findBy(['status' => 'Pendiente']);

        if (count($orders) > 0) 
        {
            $orders = array_filter($orders, function($order) use ($restaurant) {
                return $order->getRestaurant()->getId() == $restaurant;
            });

            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order, $entityManager);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No pending orders found.'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/complete/{restaurant}', name: 'app_order_complete', methods: ['GET'])]
    public function getCompleteOrders(OrderRepository $orderRepository, EntityManagerInterface $entityManager, string $restaurant): JsonResponse
    {
        $orders = $orderRepository->findBy(['status' => 'Completado']);

        if (count($orders) > 0) 
        {
            $orders = array_filter($orders, function($order) use ($restaurant) {
                return $order->getRestaurant()->getId() == $restaurant;
            });

            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order, $entityManager);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No completed orders found.'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/cancelled/{restaurant}', name: 'app_order_cancelled', methods: ['GET'])]
    public function getCancelledOrders(OrderRepository $orderRepository, EntityManagerInterface $entityManager, string $restaurant): JsonResponse
    {
        $orders = $orderRepository->findBy(['status' => 'Cancelado']);

        if (count($orders) > 0) 
        {
            $orders = array_filter($orders, function($order) use ($restaurant) {
                return $order->getRestaurant()->getId() == $restaurant;
            });

            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order, $entityManager);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No cancelled orders found.'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/restaurant/{id}', name: 'app_order_restaurant', methods: ['GET'])]
    public function getRestaurantOrders(OrderRepository $orderRepository, $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $orders = $orderRepository->findBy(['restaurant' => $id]);

        if (count($orders) > 0) 
        {
            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order, $entityManager);
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

    private function generateImageUrl($image): string
    {
        return 'data:image/jpeg;base64,'.base64_encode(stream_get_contents($image));
    }

    #[Route('/user', name: 'app_user_orders', methods: ['POST'])]
    public function getUserOrders(OrderRepository $orderRepository, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $email = $data["email"];

        $client = $entityManager->getRepository(Client::class)->findOneBy(['email' => $email]);
        $orders = $orderRepository->findBy(['client' => $client]);

        if (count($orders) > 0) 
        {
            $orderList = [];
            foreach ($orders as $order) {
                $orderList[] = $this->orderModel($order, $entityManager);
            }
            return new JsonResponse($orderList, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'No orders found for this user.'], Response::HTTP_NOT_FOUND);
    }
}

