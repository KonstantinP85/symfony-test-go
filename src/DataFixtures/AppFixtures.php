<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->uploadUserData() as $item) {
            $user = new User();
            $user->setRoles($item['roles']);
            $user->setLogin($item['login']);
            $user->setPassword('');
            $manager->persist($user);

            $user->setPassword($this->passwordHasher->hashPassword($user, $item['password']));
        }

        $manager->flush();
    }

    private function uploadUserData(): array
    {
        return [
            [
                'roles' => ['ROLE_GUEST'],
                'login' => 'guest',
                'password' => '12345'
            ],
            [
                'roles' => ['ROLE_AUTHOR'],
                'login' => 'author',
                'password' => '12345'
            ],
            [
                'roles' => ['ROLE_MODERATOR'],
                'login' => 'moderator',
                'password' => '12345'
            ]
        ];
    }
}
