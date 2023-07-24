<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
    //     $videoList = [
    //         'Nose Press/Tail Press' => 'https://www.youtube.com/watch?v=P72Q5XGMyDo',
    //         'Butter' => 'https://www.youtube.com/watch?v=SSgmBfHjYbM',
    //         'Ollie/Nollie' => 'https://www.youtube.com/watch?v=aAzP3wNT220',
    //         'Jib' => 'https://www.youtube.com/watch?v=Scpvby37V_E',
    //         'Frontside/Backside 180-360' => 'https://www.youtube.com/watch?v=hUddT6FGCws',
    //         'Indy Grab' => 'https://www.youtube.com/watch?v=6yA3XqjTh_w',
    //         'Tail Grab' => 'https://www.youtube.com/watch?v=YAElDqyD-3I',
    //         'Nose Grab' => 'https://www.youtube.com/watch?v=y2MHu0mbzQw',
    //         'Backflip' => 'https://www.youtube.com/watch?v=SlhGVnFPTDE',
    //         'Frontside Cork 540' => 'https://www.youtube.com/watch?v=FMHiSF0rHF8',
    //         'Rocket Air' => 'https://www.youtube.com/watch?v=ECuPRQPmHpA',
    //         'Tuck Knee' => 'https://www.youtube.com/watch?v=X_WhGuIY9Ak',
    //         'Wildcat' => 'https://www.youtube.com/watch?v=7KUpodSrZqI',
    //         'Frontside Lipslide' => 'https://www.youtube.com/watch?v=LSVn5aI56aU',
    //         'Tamedog Front Flip' => 'https://www.youtube.com/watch?v=eGJ8keB1-JM',
    //         'Chicken Salad' => 'https://www.youtube.com/watch?v=TTgeY_XCvkQ',
    //         'Bloody Dracula' => 'https://www.youtube.com/watch?v=UU9iKINvlyU',
    //         '1980' => 'https://www.youtube.com/watch?v=AKfeui9yrw4',
    //         'Frontside 360' => 'https://www.youtube.com/watch?v=9T5AWWDxYM4',
    //     ];
    //     foreach ($videoList as $name => $videoUrl) {
    //         $video = new Video();
    //         $i = 0;
    //         $trick = $this->getReference('trick_' . $i);
    //         while ($trick !== null) {
    //             if ($trick->getName() === $name) {
    //                 $video->setTrick($trick);
    //                 break;
    //             } 
    //             $i++;
    //             if ($i >= 20) {
    //                 break;
    //             }
    //             $trick = $this->getReference('trick_' . $i);
    //         }
    //         $video->setIframe($videoUrl);
    //         $video->setUuid(Uuid::v6());

    //         $manager->persist($video);
    //     }
    //     $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
