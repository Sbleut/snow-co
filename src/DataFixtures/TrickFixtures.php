<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Video;
use App\Entity\ProfilPic;
use App\Service\Slug;
use App\Entity\Category;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;


class TrickFixtures extends Fixture
{
    public function __construct(private PasswordHasherFactoryInterface $passwordHasherFactory, public Slug $slug)
    {
    }


    public function load(ObjectManager $manager)
    {
        $trickList = [
            'Nose Press/Tail Press',
            'Butter',
            'Ollie/Nollie',
            'Jib',
            'Frontside/Backside 180-360',
            'Indy Grab',
            'Tail Grab',
            'Nose Grab',
            'Backflip',
            'Frontside Cork 540',
            'Rocket Air',
            'Tuck Knee',
            'Truck Driver',
            'Wildcat',
            'Lipslide',
            'Tamedog Front Flip',
            'Fresh Fish',
            'Chicken Salad',
            'Bloody Dracula',
            '1980',
            'Frontside 360',
        ];
        $videoList = [
            'Nose Press/Tail Press' => 'https://www.youtube.com/watch?v=P72Q5XGMyDo',
            'Butter' => 'https://www.youtube.com/watch?v=SSgmBfHjYbM',
            'Ollie/Nollie' => 'https://www.youtube.com/watch?v=aAzP3wNT220',
            'Jib' => 'https://www.youtube.com/watch?v=Scpvby37V_E',
            'Frontside/Backside 180-360' => 'https://www.youtube.com/watch?v=hUddT6FGCws',
            'Indy Grab' => 'https://www.youtube.com/watch?v=6yA3XqjTh_w',
            'Tail Grab' => 'https://www.youtube.com/watch?v=YAElDqyD-3I',
            'Nose Grab' => 'https://www.youtube.com/watch?v=y2MHu0mbzQw',
            'Backflip' => 'https://www.youtube.com/watch?v=SlhGVnFPTDE',
            'Frontside Cork 540' => 'https://www.youtube.com/watch?v=FMHiSF0rHF8',
            'Rocket Air' => 'https://www.youtube.com/watch?v=ECuPRQPmHpA',
            'Tuck Knee' => 'https://www.youtube.com/watch?v=X_WhGuIY9Ak',
            'Wildcat' => 'https://www.youtube.com/watch?v=7KUpodSrZqI',
            'Frontside Lipslide' => 'https://www.youtube.com/watch?v=LSVn5aI56aU',
            'Tamedog Front Flip' => 'https://www.youtube.com/watch?v=eGJ8keB1-JM',
            'Chicken Salad' => 'https://www.youtube.com/watch?v=TTgeY_XCvkQ',
            'Bloody Dracula' => 'https://www.youtube.com/watch?v=UU9iKINvlyU',
            '1980' => 'https://www.youtube.com/watch?v=AKfeui9yrw4',
            'Frontside 360' => 'https://www.youtube.com/watch?v=9T5AWWDxYM4',
        ];

        $categoryList = [
            'Les grabs',
            'Les rotations',
            'Les flips',
            'Les slides',
            'Les rotations désaxées',
            'Les one foot tricks',
            'Old School',
        ];

        $userList = [
            0 => ['Jimmmy', 'jimmy@hotmail.fr', 'toto'],
            1 => ['Joey', 'joey@hotmail.fr', 'titi'],
            2 => ['Sam', 'dam@gmail.com', 'sam'],
        ];
        $userObjectList = [];

        foreach ($userList as $userArray) {
            $user = new User();
            $user->setUserName($userArray[0]);
            $user->setEmail($userArray[1]);
            $user->setPassword($this->passwordHasherFactory->getPasswordHasher(User::class)->hash($userArray[2]));
            $userObjectList[] = $user;

            $manager->persist($user);
        }

        foreach ($categoryList as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $uuid = Uuid::v6($category->getName());
            $category->setUuid($uuid);

            $manager->persist($category);

            // Tricks Creation 
            foreach ($trickList as $trickName) {
                

                $trick = new Trick();
                $trick->setName($trickName);
                $trick->setDescription('lorem ipsum');

                //Random date
                // Set the start and end dates for the range
                $start = strtotime('2023-01-01 00:00:00' );
                $end = strtotime('2023-12-31 00:00:00');

                // Generate a random timestamp within the range
                $randomTimestamp = mt_rand($start, $end);

                // Create a DateTime object from the random timestamp
                $randomDate = new DateTimeImmutable();
                $trick->setCreatedAt($randomDate->setTimestamp($randomTimestamp));
                
                // Fix Slug generation 
                $trick->setSlug(strval($this->slug->generateSlug($trickName)));

                for ($i = 1; $i <= mt_rand(2, 3); $i++) {
                    $comment = new Comment();
                    $comment->setContent('lorem ipsum');
                    $days = (new \DateTime())->diff($trick->getCreatedAt())->days;
                    // Fix Date to be after article creation
                    $comment->setCreatedAt(new \DateTimeImmutable());
                    $comment->setAuthor($userObjectList[mt_rand(0, 2)]);
                    $comment->setTrick($trick);

                    $manager->persist($comment);
                }

                $manager->persist($trick);
            }
        }

        $manager->flush();
    }
}
