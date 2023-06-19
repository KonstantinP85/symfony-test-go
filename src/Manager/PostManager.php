<?php

namespace App\Manager;

use App\Dto\Post\CreatePostDto;
use App\Dto\Post\ModerationPostDto;
use App\Dto\Post\PostResponse;
use App\Entity\Post;
use App\Entity\User;
use App\Enum\PostStatus;
use App\Repository\PostRepository;
use App\Repository\RepositoryEnum;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class PostManager
{
    private SymfonySerializer $serializer;

    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
        $propertyNormalizer = new PropertyNormalizer();
        $arrayDenormalized = new ArrayDenormalizer();
        $objectNormalizer = new ObjectNormalizer(new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())));

        $this->serializer = new SymfonySerializer([$arrayDenormalized, $objectNormalizer, $propertyNormalizer], [new JsonEncoder()]);
    }

    public function getList(): array
    {
        $postList = $this->postRepository->searchPostList($this->getPostListCriteria());

        $arrayEntity = $this->serializer->normalize($postList, null,
            [
                AbstractNormalizer::CALLBACKS => [
                    'user' =>  function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
                        return $innerObject->getId();
                    },
                ],
            ]
        );

        $context = ['groups' => ['guest']];
        if ($this->security->isGranted('ROLE_AUTHOR')) {
            $context = ['groups' => ['author']];
        }

        return $this->serializer->denormalize($arrayEntity, PostResponse::class . '[]',  null, $context);
    }

    public function create(CreatePostDto $dto): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \Exception('Auth error', Response::HTTP_NOT_FOUND);
        }

        $post = new Post(
            $user,
            $dto->title,
            $dto->content,
            PostStatus::ON_MODERATION->value
        );

        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    public function edit(CreatePostDto $dto, int $id): void
    {
        $post = $this->checkAndGetPostOwner($id);

        $post->setTitle($dto->title);
        $post->setContent($dto->content);
        $post->setStatus(PostStatus::ON_MODERATION->value);

        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $post = $this->checkAndGetPostOwner($id);

        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }

    public function moderate(ModerationPostDto $dto, int $id): void
    {
        $post = $this->getPost($id);

        if (isset($dto->moderationComment)) {
            $post->setModerationComment($dto->moderationComment);
        }
        $post->setStatus($dto->status);

        $this->entityManager->flush();

    }

    private function getPost(int $id): Post
    {
        $post = $this->postRepository->find($id);

        if (!$post instanceof Post) {
            throw new \Exception('This post was not found');
        }

        return $post;
    }

    private function checkAndGetPostOwner(int $id): Post
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \Exception('Auth error', Response::HTTP_FORBIDDEN);
        }

        $post = $this->getPost($id);

        if ($post->getUser()->getId() !== $user->getId()) {
            throw new \Exception('This post is not yours', Response::HTTP_NOT_FOUND);
        }

        return $post;
    }

    private function getPostListCriteria(): Criteria
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new \Exception('Auth error', Response::HTTP_FORBIDDEN);
        }

        $filter = [];
        if (!$this->security->isGranted('ROLE_AUTHOR')) {
            $filter['status'] = PostStatus::PUBLISHED;
        }

        if ($this->security->isGranted('ROLE_AUTHOR') && !$this->security->isGranted('ROLE_MODERATOR')) {
            $filter['user'] = $user->getId();
        }

        return new Criteria($this->buildExpression($filter));
    }

    private function buildExpression(array $filter): ?CompositeExpression
    {
        if (count($filter) > 0) {
            return new CompositeExpression(
                RepositoryEnum::EXPRESSION_AND->value,
                array_map(function($key, $value) {
                    return new Comparison(
                        $key,
                        RepositoryEnum::COMPARISON_EQ->value,
                        $value,
                    );
                }, array_keys($filter), $filter)
            );
        }

        return null;
    }
}