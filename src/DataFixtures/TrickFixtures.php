<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Symfony\Component\String\Slugger\SluggerInterface;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;


class TrickFixtures extends Fixture
{
    public const TRICK_REFERENCE = 'trick';

    public function __construct(private SluggerInterface $slugger)
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


        // No Imbrication between objects Each object has its fixturz
        // Tricks Creation 
        foreach ($trickList as $trickName) {
            $trick = new Trick();
            $trick->setName($trickName);
            $trick->setDescription('lorem ipsum');

            $trick->setUser($this->getReference(UserFixtures::USER_REFERENCE));

            $trick->setUser($this->getReference(CategoryFixtures::CATEGORY_REFERENCE));
            //Random date
            // Set the start and end dates for the range
            $start = strtotime('2023-01-01 00:00:00');
            $end = strtotime('2023-12-31 00:00:00');

            // Generate a random timestamp within the range
            $randomTimestamp = mt_rand($start, $end);

            // Create a DateTime object from the random timestamp
            $randomDate = new DateTimeImmutable();
            $trick->setCreatedAt($randomDate->setTimestamp($randomTimestamp));

            // Fix Slug generation 
            $trick->setSlug($this->slugger->slug($trickName));

            $manager->persist($trick);
        }
        $manager->flush();

        $this->addReference(self::TRICK_REFERENCE, $trick);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
