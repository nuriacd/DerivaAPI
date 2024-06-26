<?php
namespace App\Controller;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/restaurant')]
class RestaurantController extends AbstractController
{
    #[Route('/', name: 'app_restaurant_index', methods: ['GET'])]
    public function index(RestaurantRepository $restaurantRepository): JsonResponse
    {
        $restaurants = $restaurantRepository->findAll();
        
        if (count($restaurants) > 0) 
        {
            $restaurantList = [];
            foreach ($restaurants as $restaurant) {
                $restaurantList[] = $this->restaurantModel($restaurant);
            }
            return new JsonResponse($restaurantList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No restaurants found.'], Response::HTTP_NOT_FOUND);
    }

    private function restaurantModel(Restaurant $restaurant): array
    {
        return 
        [
            'name'  => $restaurant->getName(),
            //'location'  => $restaurant->getLocation(),
        ];
    }

    #[Route('/new', name: 'app_restaurant_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $name = $data["name"]; 
        $location = $data["location"]; 

        $restaurant = new Restaurant();
        $restaurant->setName($name);
        //$restaurant->setLocation($location);

        $errors = $validator->validate($restaurant);
        if (count($errors)  > 0) 
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        
        $entityManager->persist($restaurant);
        $entityManager->flush();

        return new JsonResponse (['message' => 'Restaurant created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_restaurant_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, string $id): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => $id]);
        if (!$restaurant)
            return new JsonResponse(['message' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        
        $restaurant = $this->restaurantModel($restaurant);
        return new JsonResponse($restaurant, Response::HTTP_OK);
    }

    #[Route('/name/{id}', name: 'app_restaurant_name', methods: ['GET'])]
    public function getName(EntityManagerInterface $entityManager, string $id): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($id);
        if (!$restaurant)
            return new JsonResponse(['message' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        
        $restaurant = $restaurant->getName();
        return new JsonResponse($restaurant, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_restaurant_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $name = $data["name"]; 
        $location = $data["location"]; 

        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => $id]);
        if (!$restaurant)
            return new JsonResponse(['message' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        
        $restaurant->setName($name);
        //$restaurant->setLocation($location);

        $errors = $validator->validate($restaurant);
        if (count($errors)  > 0)
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        
        $entityManager->flush();

        return new JsonResponse (['message' => 'Restaurant updated successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_restaurant_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => $id]);
        if (!$restaurant)
            return new JsonResponse(['message' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        
        $entityManager->remove($restaurant);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Restaurant deleted successfully'], Response::HTTP_OK);
    }

    public function getRestaurant(EntityManagerInterface $entityManager, string $id): Restaurant | bool
    {
        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => $id]);

        return $restaurant ? $restaurant : false;
        
    }

    #[Route('/delivery/{city}', name: 'app_restaurant_by_delivery_city', methods: ['GET'])]
    public function getRestaurantByDeliveryCity(RestaurantRepository $restaurantRepository, string $city): JsonResponse
    {
        $restaurant = $restaurantRepository->findOneBy(['deliveryCity' => $city]);

        if ($restaurant) {
            $id = $restaurant->getId();
            return new JsonResponse(['id' => $id], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'No restaurants found in the specified delivery city.'], Response::HTTP_NOT_FOUND);
    }
}