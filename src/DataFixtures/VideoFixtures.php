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
        $videoList = [
            0 => ['https://www.youtube.com/watch?v=P72Q5XGMyDo', 0],
            1 => ['https://www.youtube.com/watch?v=SSgmBfHjYbM', 1],
            2 => ['https://www.youtube.com/watch?v=aAzP3wNT220', 2],
            3 => ['https://www.youtube.com/watch?v=Scpvby37V_E', 3],
            4 => ['https://www.youtube.com/watch?v=hUddT6FGCws', 4],
            5 => ['https://www.youtube.com/watch?v=6yA3XqjTh_w', 5],
            6 => ['https://www.youtube.com/watch?v=YAElDqyD-3I', 6],
            7 => ['https://www.youtube.com/watch?v=y2MHu0mbzQw', 7],
            8 => ['https://www.youtube.com/watch?v=SlhGVnFPTDE', 8],
            9 => ['https://www.youtube.com/watch?v=FMHiSF0rHF8', 9],
            10 =>['https://www.youtube.com/watch?v=ECuPRQPmHpA', 10],
            11 =>['https://www.youtube.com/watch?v=X_WhGuIY9Ak', 11],
            12 =>['https://www.youtube.com/watch?v=7KUpodSrZqI', 12],
            13 =>['https://www.youtube.com/watch?v=LSVn5aI56aU', 13],
            14 =>['https://www.youtube.com/watch?v=eGJ8keB1-JM', 14],
            15 =>['https://www.youtube.com/watch?v=TTgeY_XCvkQ', 15],
            16 =>['https://www.youtube.com/watch?v=UU9iKINvlyU', 16],
            17 =>['https://www.youtube.com/watch?v=AKfeui9yrw4', 17],
            18 =>['https://www.youtube.com/watch?v=9T5AWWDxYM4', 18],
        ];

        foreach ($videoList as $videoUrl) {
            $video = new Video();
            $video->setTrick($this->getReference('trick_' . $videoUrl[1]));
            $video->setIframe($videoUrl[0]);
            $video->setUuid(Uuid::v6());
            $manager->persist($video);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
