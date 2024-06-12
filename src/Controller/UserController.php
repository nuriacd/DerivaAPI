<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\EmployeeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        
        if (count($users) > 0) 
        {
            foreach ($users as $user) {
                $userList = [];

                if ($user instanceof Client) {
                    $userList[] = $this->clientModel($user);
                } elseif ($user instanceof Employee) {
                    $userList[] = $this->employeeModel($user); 
                }
            }

            return new JsonResponse($userList, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'No users found.'], Response::HTTP_NOT_FOUND);
    }

    private function clientModel(Client $user): array
    {
        return 
        [
            'id'  => $user->getId(),
            'name'  => $user->getName(),
            'email'  => $user->getEmail(),
            'phone'  => $user->getPhone(),
        ];
    }

    private function employeeModel(Employee $user): array
    {
        return
        [
            'id'  => $user->getId(),
            'name'  => $user->getName(),
            'email'  => $user->getEmail(),
            'phone'  => $user->getPhone(),
            'type' => $user->getType(),
            'restaurant' => $user->getRestaurant()->getId(),
        ];
    }

    #[Route('/clients', name: 'app_client_index', methods: ['GET'])]
    function getClients(ClientRepository $clientRepository): JsonResponse
    {
        $users = $clientRepository->findAll();

        if (count($users) > 0) 
        {
            $clients = [];

            foreach ($users as $user) {
                $clients[] = $this->clientModel($user);
            }
            return new JsonResponse($clients, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'No users found.'], Response::HTTP_NOT_FOUND);

    }

    #[Route('/employees', name: 'app_employees_index', methods: ['GET'])]
    function getEmployees(EmployeeRepository $employeeRepository): JsonResponse
    {
        $users = $employeeRepository->findAll();

        if (count($users) > 0) 
        {
            $employees = [];

            foreach ($users as $user) {
                $employees[] = $this->employeeModel($user);
            }
            return new JsonResponse($employees, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'No users found.'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/new', name: 'app_user_new', methods: ['POST'])]
    public function new(JWTTokenManagerInterface $JWTManager, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $name = $data["name"]; $email = $data["email"]; $phone = $data["phone"]; $pwd = $data["password"]; $pwd2 = $data["password2"];

        $validPwd = $this->checkPwd($pwd, $pwd2);
        $validPhone = $this->checkPhone($phone);

        if ($validPwd && $validPhone)
        {
            $user = new Client();

            $user->setName($name);
            $user->setEmail($email);
            $user->setPhone($phone);
            $user->setPassword($this->hashPwd($passwordHasher, $pwd, $user));

            $errors = $validator->validate($user);
            if (count($errors)  > 0) 
                return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            
            $entityManager->persist($user);
            $entityManager->flush();
    
            $token = $JWTManager->create($user);  
            return new JsonResponse(['token' => $token], Response::HTTP_OK);
        }

        return new JsonResponse (['message' => 'Incorrect password and/or phone'], Response::HTTP_BAD_REQUEST);

    }
    
    #[Route('/new/employee', name: 'app_employee_new', methods: ['POST'])]
    public function newEmployee(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $name = $data["name"]; $email = $data["email"]; $phone = $data["phone"]; $pwd = $data["password"]; $pwd2 = $data["password2"];
        $type = "employee";

        $restaurant = $entityManager->getRepository(Restaurant::class)->findOneBy(['name' => $data["restaurant"]]);

        $validPwd = $this->checkPwd($pwd, $pwd2);
        $validPhone = $this->checkPhone($phone);

        if ($validPwd && $validPhone)
        {
            $employee = new Employee();

            $employee->setName($name);
            $employee->setEmail($email);
            $employee->setPhone($phone);
            $employee->setType($type);
            $employee->setRoles(['ROLE_EMPLOYEE']);
            $employee->setRestaurant($restaurant);

            $employee->setPassword($this->hashPwd($passwordHasher, $pwd, $employee));

            $errors = $validator->validate($employee);
            if (count($errors)  > 0) 
                return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            
            $entityManager->persist($employee);
            $entityManager->flush();

            return new JsonResponse (['message' => 'Employee created successfully'], Response::HTTP_CREATED);
        }

        return new JsonResponse (['message' => 'Incorrect password and/or phone'], Response::HTTP_BAD_REQUEST);
    }

    private function checkPwd(string $pwd, string $pwd2): bool
    {
        if (strlen($pwd) == 0 || strlen($pwd2) == 0)
            return false;
        else if ($pwd !== $pwd2)
            return false;
        else if (! preg_match('/^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%!?*.])[0-9A-Za-z!@#$%!?*.]{8,}$/', $pwd))
            return false;

        return true;
    }

    private function checkPhone($phone) : bool 
    {
        if (!preg_match('/^[9|8|6|7][0-9]{8}$/', $phone)) 
            return false;
        
        return true;
    }

    private function hashPwd (UserPasswordHasherInterface $passwordHasher, $plaintextPassword, $user): string
    {
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        return $hashedPassword;
    }

    #[Route('/{id}/get', name: 'app_user_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, string $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user)
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);

            if ($user instanceof Client) {
                $user = $this->clientModel($user);
            } elseif ($user instanceof Employee) {
                $user = $this->employeeModel($user);
            }

        return new JsonResponse($user, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $name = $data["name"]; $email = $data["email"]; $phone = $data["phone"];

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user)
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);

        $validPhone = $this->checkPhone($phone);

        if ($validPhone)
        {
            $user->setName($name);
            $user->setEmail($email);
            $user->setPhone($phone);

            $errors = $validator->validate($user);
            if (count($errors)  > 0) 
                return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            
            $entityManager->flush();
    
            return new JsonResponse (['message' => 'User updated successfully'], Response::HTTP_OK);
        }

        return new JsonResponse (['message' => 'Incorrect or phone'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/editPwd', name: 'app_pwd_edit', methods: ['PUT'])]
    public function editPwd(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $pwd = $data["pwd"]; $pwd2 = $data["pwd2"];

        $user = $entityManager->getRepository(User::class)->find($id);
        
        if (!$user)
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);

        $validPwd = $this->checkPwd($pwd, $pwd2);
        
        if ($validPwd)
        {
            $user->setPassword($this->hashPwd($passwordHasher, $pwd, $user));
            
            $errors = $validator->validate($user);
            if (count($errors)  > 0) 
                return new JsonResponse(['message' => (string) $errors], Response::HTTP_BAD_REQUEST);
            
            $entityManager->flush();
    
            return new JsonResponse (['message' => 'Password updated successfully'], Response::HTTP_OK);
        }

        return new JsonResponse (['message' => 'Incorrect or phone'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user)
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User deleted successfully'], Response::HTTP_OK);
    }

    #[Route('/login', name: 'app_user_login', methods: ['POST'])]
    public function login (JWTTokenManagerInterface $JWTManager, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data["loginEmail"]; $password = $data["loginPassword"];
        
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user && $passwordHasher->isPasswordValid($user, $password))
        {
            $token = $JWTManager->create($user);  
            return new JsonResponse(['token' => $token], Response::HTTP_OK);
        }
        
        return new JsonResponse (['message' => 'Incorrect credentials'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}/checkPwd', name: 'app_user_checkPwd', methods: ['POST'])]
    public function checkPassword (EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Request $request, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $password = $data["password"];

        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user && $passwordHasher->isPasswordValid($user, $password)) 
            return new JsonResponse (['message' => 'Correct credentials'], Response::HTTP_OK);

        return new JsonResponse (['message' => 'Incorrect credentials'], Response::HTTP_OK);
    }
}

