<?php

namespace App\Controller;

use App\Dto\Post\CreatePostDto;
use App\Dto\Post\ModerationPostDto;
use App\Manager\PostManager;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostController extends AbstractController
{
    #[Route('/api/post/list', name: 'app_post_list', methods: 'GET')]
    #[IsGranted('ROLE_GUEST', message: 'You are not allowed to access the list.')]
    #[OA\Tag(name: 'Посты')]
    public function list(PostManager $manager): Response
    {
        $postList = $manager->getList();

        return new JsonResponse($postList, Response::HTTP_OK);
    }

    #[Route('/api/post/create', name: 'app_post_create', methods: ['POST'])]
    #[IsGranted('ROLE_AUTHOR', message: 'You are not allowed to access to create post.')]
    #[OA\Tag(name: 'Посты')]
    #[OA\RequestBody(
        content: [new OA\MediaType(mediaType: "application/json",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "content", type: "string")
                ]
            )
        )],
    )]
    #[OA\Response(
        response: 201,
        description: 'Операция выполнена'
    )]
    #[OA\Response(
        response: 400,
        description: 'Ошибка в параметрах'
    )]
    public function create(Request $request, SerializerInterface  $serializer, ValidatorInterface $validator, PostManager $manager): Response
    {
        $dto = $serializer->deserialize($request->getContent(), CreatePostDto::class, 'json');
        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            throw new BadRequestHttpException((string) $violations);
        }
        $manager->create($dto);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    #[Route('/api/post/edit/{id}', name: 'app_post_edit', methods: ['PUT'])]
    #[IsGranted('ROLE_AUTHOR', message: 'You are not allowed to access to edit post.')]
    #[OA\Tag(name: 'Посты')]
    #[OA\RequestBody(
        content: [new OA\MediaType(mediaType: "application/json",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "content", type: "string")
                ]
            )
        )],
    )]
    #[OA\Response(
        response: 200,
        description: 'Операция выполнена'
    )]
    #[OA\Response(
        response: 400,
        description: 'Ошибка в параметрах'
    )]
    public function edit(Request $request, SerializerInterface  $serializer, ValidatorInterface $validator, PostManager $manager, int $id): Response
    {
        $dto = $serializer->deserialize($request->getContent(), CreatePostDto::class, 'json');
        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            throw new BadRequestHttpException((string) $violations);
        }
        $manager->edit($dto, $id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/post/delete/{id}', name: 'app_post_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_AUTHOR', message: 'You are not allowed to access to delete post.')]
    #[OA\Tag(name: 'Посты')]
    #[OA\Response(
        response: 200,
        description: 'Операция выполнена'
    )]
    #[OA\Response(
        response: 404,
        description: 'Пост не найден'
    )]
    public function delete(PostManager $manager, int $id): Response
    {
        $manager->delete($id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/post/moderate/{id}', name: 'app_post_moderate', methods: ['PATCH'])]
    #[IsGranted('ROLE_MODERATOR', message: 'You are not allowed to access to moderate post.')]
    #[OA\Tag(name: 'Посты')]
    #[OA\RequestBody(
        content: [new OA\MediaType(mediaType: "application/json",
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: "moderation_comment", type: "string"),
                    new OA\Property(property: "status", type: "integer")
                ]
            )
        )],
    )]
    #[OA\Response(
        response: 200,
        description: 'Операция выполнена'
    )]
    #[OA\Response(
        response: 404,
        description: 'Пост не найден'
    )]
    public function moderate(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, PostManager $manager, int $id): Response
    {
        $dto = $serializer->deserialize($request->getContent(), ModerationPostDto::class, 'json');
        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            throw new BadRequestHttpException((string) $violations);
        }
        $manager->moderate($dto, $id);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}