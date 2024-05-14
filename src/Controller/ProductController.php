<?php
namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        
        if (count($products) > 0) 
        {
            $productList = [];
            foreach ($products as $product) {
                $productList[] = $this->productModel($product);
                if ($product instanceof Dish) {
                    $productData['type'] = $product->getType();
                    $productData['recipe'] = $product->getRecipe();
                    $productData['ingredients'] = $product->getIngredients()->toArray();
                }
            }
            return new JsonResponse($productList, Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'No products found.'], Response::HTTP_NOT_FOUND);
    }

    private function productModel(Product $product): array
    {
        return 
        [
            'id'  => $product->getId(),
            'name'  => $product->getName(),
            'price'  => $product->getPrice(),
            'description'  => $product->getDescription(),
        ];
    }

    #[Route('/new', name: 'app_product_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $name = $data["name"]; 
        $price = $data["price"]; 
        $description = $data["description"]; 

        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);

        $errors = $validator->validate($product);
        if (count($errors)  > 0) 
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        
        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse (['message' => 'Product created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, string $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product)
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);

        $productData = $this->productModel($product);
        if ($product instanceof Dish) {
            $productData['type'] = $product->getType();
            $productData['recipe'] = $product->getRecipe();
            $productData['ingredients'] = $product->getIngredients()->toArray();
        }
        $productList[] = $productData;        
        
        return new JsonResponse($productList, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $name = $data["name"]; 
        $price = $data["price"]; 
        $description = $data["description"]; 

        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product)
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);

        if ($product instanceof Dish) {
            $type = $data["type"];
            $recipe = $data["recipe"];
            $product->setType($type);
            $product->setRecipe($recipe);
        }

        $errors = $validator->validate($product);
        if (count($errors)  > 0) 
            return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
        
        $entityManager->flush();
        return new JsonResponse (['message' => 'Product updated successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product)
            return new JsonResponse(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        
        $entityManager->remove($product);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Product deleted successfully'], Response::HTTP_OK);
    }
}