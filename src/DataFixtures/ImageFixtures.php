<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

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
        $directory = "public/uploads/image";
        $contents = scandir($directory);
        foreach ($contents as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $directory . DIRECTORY_SEPARATOR . $item;
            $files = scandir($path);
            foreach ($files as $file) {
                if (is_dir($path)) {
                    $image = new Image();
                    $image->setFileName($file);
                    $i = 0;
                    $trick = $this->getReference('trick_' . $i);
                    while ($trick !== null) {
                        if ($trick->getSlug() === $item) {
                            var_dump('FOund for this '. $trick->getName());
                            $image->setTrick($trick);
                            break; // On arrête la boucle car on a trouvé le trick correspondant
                        }
                        $i++;
                        if($i>=20){
                            break;
                        }
                        $trick = $this->getReference('trick_' . $i);
                    }
                }
            }
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        return [
            TrickFixtures::class,
        ];
    }
}
