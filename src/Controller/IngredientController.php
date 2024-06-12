<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Restaurant;
use App\Entity\RestaurantIngredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/ingredient')]
class IngredientController extends AbstractController {

    #[Route('/', name: 'app_ingredients_list', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $ingredients = $entityManager->getRepository(Ingredient::class)->findAll();
        if (!$ingredients) {
            return new JsonResponse(['message' => 'No ingredients found'], Response::HTTP_NOT_FOUND);
        }

        $ingredientsArray = [];
        foreach ($ingredients as $ingredient) {
            $ingredientsArray[] = [
                'id' => $ingredient->getId(),
                'name' => $ingredient->getName(),
                'allergen' => $ingredient->getAllergen(),
            ];
        }
        return new JsonResponse($ingredientsArray, Response::HTTP_OK);
    } 

    #[Route('/new', name: 'app_ingredient_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ingredient = new Ingredient();
        $ingredient->setName($data['name']);
        $ingredient->setAllergen($data['allergen']);

        $errors = $validator->validate($ingredient);
        if (count($errors) > 0) {
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($ingredient);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Ingredient created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_ingredient_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        $ingredient = $entityManager->getRepository(Ingredient::class)->find($id);
        if (!$ingredient) {
            return new JsonResponse(['message' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        $ingredientData = [
            'id' => $ingredient->getId(),
            'name' => $ingredient->getName(),
            'allergen' => $ingredient->getAllergen(),
        ];

        return new JsonResponse($ingredientData, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_ingredient_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, string $id): JsonResponse
    {
        $ingredient = $entityManager->getRepository(Ingredient::class)->find($id);
        if (!$ingredient) {
            return new JsonResponse(['message' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $ingredient->setName($data['name']);
        $ingredient->setAllergen($data['allergen']);

        $errors = $validator->validate($ingredient);
        if (count($errors) > 0) {
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Ingredient updated successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_ingredient_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, string $id): JsonResponse
    {
        $ingredient = $entityManager->getRepository(Ingredient::class)->find($id);
        if (!$ingredient) {
            return new JsonResponse(['message' => 'Ingredient not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($ingredient);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Ingredient deleted successfully'], Response::HTTP_OK);
    }

    #[Route('/restaurant/{restaurantId}', name: 'app_restaurant_ingredients_list', methods: ['GET'])]
    public function listRestaurantIngredients(int $restaurantId, EntityManagerInterface $entityManager): Response
    {
        $allIngredients = $entityManager->getRepository(Ingredient::class)->findAll();
        $restaurantIngredients = $entityManager->getRepository(RestaurantIngredient::class)->findBy(['restaurant_id' => $restaurantId]);
        
        $restaurantIngredientsMap = [];
        foreach ($restaurantIngredients as $restaurantIngredient) {
            $restaurantIngredientsMap[$restaurantIngredient->getIngredientId()->getId()] = $restaurantIngredient->getQuantity();
        }
    
        $ingredientsList = [];
        foreach ($allIngredients as $ingredient) {
            $ingredientId = $ingredient->getId();
            $ingredientsList[] = [
                'id' => $ingredientId,
                'name' => $ingredient->getName(),
                'allergen' => $ingredient->getAllergen(),
                'quantity' => $restaurantIngredientsMap[$ingredientId] ?? 0, // Si el ingrediente no está en el mapa, su cantidad es 0
            ];
        }
    
        return new JsonResponse($ingredientsList, Response::HTTP_OK);
    }

    #[Route('/restaurant/{restaurantId}/update', name: 'app_restaurant_ingredient_update', methods: ['PUT'])]
    public function updateRestaurantIngredient(int $restaurantId, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ingredientId = $data['ingredientId'];
        $quantity = $data['quantity'];

        $restaurantId = $entityManager->getRepository(Restaurant::class)->find($restaurantId);
        $ingredientId = $entityManager->getRepository(Ingredient::class)->find($ingredientId);

        // Buscar el ingrediente del restaurante específico
        $restaurantIngredient = $entityManager->getRepository(RestaurantIngredient::class)->findOneBy([
            'restaurant_id' => $restaurantId,
            'ingredient_id' => $ingredientId,
        ]);

        if (!$restaurantIngredient) {
            // Si el ingrediente no existe para el restaurante, crear uno nuevo
            $restaurantIngredient = new RestaurantIngredient();
            $restaurantIngredient->setRestaurantId($restaurantId);
            $restaurantIngredient->setIngredientId($ingredientId);
            $restaurantIngredient->setQuantity($quantity);
            $entityManager->persist($restaurantIngredient);
        } else {
            // Si el ingrediente ya existe, actualizar la cantidad
            $restaurantIngredient->setQuantity($quantity);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Ingredient quantity updated successfully'], Response::HTTP_OK);
    }



}