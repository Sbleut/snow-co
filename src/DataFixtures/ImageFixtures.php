<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Uid\Uuid;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        //Image 
        /**
         * dossier /public/uploads/image pour les vrais images à DL 
         * dossier /public/asset/image pour les images du site.
         * 
         * Set Main de manière aléatoire
         * Tous les cas de figure pour les images
         * 
         * Certains tricks sans image
         * 
         */
        // $product = new Product();
        // $manager->persist($product);
        $imageList = [
            0 => ['AgAAAB0A03T-dhU3O3-c4mwHr3XFvQ.avif', 0, 0],
            1 => ['AgAAAB0A12uL7BdiOP-OjJShQ0IiBw.avif', 0, 0],
            2 => ['Comment-faire-un-butter-en-snowboard-.avif', 0, 1],
            3 => ['Snowboard-School-Maz-doing-a-tailpress-or-about-to-fall.jpg', 0, 0],
            4 => ['best-snowboards-for-butter-tricks.jpg', 1, 1],
            5 => ['ollie-snowboard-freestyle-e1452772535411.jpg', 2, 0],
            6 => ['PISTE_Ollie.jpg', 2, 1],
            7 => ['212_1080x.webp', 3, 0],
            8 => ['Snowboard-Grind.jpg', 3, 1],
            9 => ['Cab-180.jpg', 4, 0],
            10 => ['fs360-Web-Ed-Blomfield-featured.jpg', 4, 1],
            11 => ['http __wordpress-604950-1959020.cloudwaysapps.com_wp-content_uploads_2022_04_jon07178-1.avif', 5, 0],
            12 => ['Trick-Indy-Grab-620x447.jpg', 5, 1],
            13 => ['tail-grab-1.jpg', 6, 1],
            14 => ['3_b5916b1c-dec5-4882-8e5d-abf311e254b3_large.jpg', 7, 1],
            15 => ['AdobeStock_486678655-1030x687.jpeg', 8, 0],
            16 => ['images.jpg', 8, 0],
            17 => ['KBS_niseko-cayley-alger_160307_210-Edit.webp', 8, 1],

        ];
        foreach ($imageList as $file) {
            $image = new Image();
            $image->setFileName($file[0]);
            $image->setTrick($this->getReference('trick_' . $file[1]));
            $image->setMainImage($file[2]);
            $image->setUuid(Uuid::v6());
            $manager->persist($image);
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
