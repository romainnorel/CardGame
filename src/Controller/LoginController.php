<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    private EntityManagerInterface $em;
    private JWTTokenManagerInterface $jwtManager;
    public function __construct(EntityManagerInterface $em, JWTTokenManagerInterface $jWTManager)
    {
        $this->em = $em;
        $this->jwtManager = $jWTManager;
    }

    #[Route('/api/login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            return new JsonResponse(['error' => 'Player not found'], 404);
        }

        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid password'], 403);
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $token], 200);
    }

    #[Route('/api/register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
    
        if (!$username || !$password) {
            return new JsonResponse(['message' => 'Nom d’utilisateur ou mot de passe manquant.'], 400);
        }
    
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Ce nom d’utilisateur existe déjà.'], 400);
        }
    
        $user = new User();
        $user->setUsername($username);
        $user->setRoles(['ROLE_PLAYER']);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
    
        $this->em->persist($user);
        $this->em->flush();
    
        return new JsonResponse(['message' => 'Utilisateur créé.'], 201);
    }
}
