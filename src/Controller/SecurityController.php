<?php

namespace App\Controller;

use App\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class SecurityController extends AbstractController
{
    #[Route('/login', name: 'api_security_login', methods: ['POST'])]
    #[OA\Tag(name: 'Безопасность')]
    #[OA\RequestBody(
        content: [new OA\MediaType(mediaType: "application/json",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "login", type: "string"),
                    new OA\Property(property: "password", type: "string")
                ]
            )
        )],
    )]
    #[OA\Response(
        response: 200,
        description: 'Операция выполнен'
    )]
    #[OA\Response(
        response: 400,
        description: 'Ошибка авторизации'
    )]
    public function login(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \Exception(Response::HTTP_BAD_REQUEST, 'Invalid credentials');
        }

        return $this->json([
            'username' => $user->getLogin(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/logout', name: 'api_security_logout', methods: ['GET'])]
    #[OA\Tag(name: 'Безопасность')]
    #[OA\Response(
        response: 200,
        description: 'Операция выполнен'
    )]
    public function logout(): void
    {
        throw new \Exception('Logout exception');
    }
}