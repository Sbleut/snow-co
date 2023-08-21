<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

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
        

        foreach ($userList as $key => $userArray) {
            $user = new User();
            $user->setUsername($userArray[0])
                ->setEmail($userArray[1])
                ->setUuid(Uuid::v6())
                ->setIsVerified(true);
            $passwordhashed = $this->hasher->hashPassword(
                $user,
                $userArray[2]
            );
            $user->setPassword($passwordhashed);

            $this->addReference('user_'.$key, $user);

            $manager->persist($user);

        }

        $manager->flush();

        

       
    }
}
