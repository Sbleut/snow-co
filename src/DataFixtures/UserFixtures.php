<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE = 'user';

    public function __construct(private UserPasswordHasherInterface $hasher,)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $userList = [
            0 => ['Jimmmy', 'jimmy@hotmail.fr', 'tototo'],
            1 => ['Joey', 'joey@hotmail.fr', 'tititi'],
            2 => ['Sam', 'dam@gmail.com', 'samsam'],
        ];

        foreach ($userList as $userArray) {
            $user = new User();
            $user->setUsername($userArray[0])
                ->setEmail($userArray[1])
                ->setIsVerified(true);
            $passwordhashed = $this->hasher->hashPassword(
                $user,
                $userArray[2]
            );
            $user->setPassword($passwordhashed);

            $userObjectList[] = $user;

            $manager->persist($user);
        }

        $manager->flush();

        $this->addReference(self::USER_REFERENCE, $user);
    }
}
