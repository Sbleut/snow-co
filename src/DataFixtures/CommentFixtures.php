<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 1; $i <= mt_rand(2, 3); $i++) {
            $comment = new Comment();
            /**
             * Create Real fake Comments 
             */
            $comment->setContent('lorem ipsum');
            $days = (new \DateTime())->diff($trick->getCreatedAt())->days;
            // Fix Date to be after article creation
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setAuthor($userObjectList[mt_rand(0, 2)]);
            $comment->setTrick($trick);

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            TrickFixtures::class,
        ];
    }
}
